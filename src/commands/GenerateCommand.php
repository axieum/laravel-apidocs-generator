<?php

namespace Axieum\ApiDocs\Commands;

use Axieum\ApiDocs\mutators\RouteMutator;
use Axieum\ApiDocs\preflight\PreflightDegree;
use Axieum\ApiDocs\preflight\RoutePreflight;
use Axieum\ApiDocs\util\DocRoute;
use Axieum\ApiDocs\util\RouteHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactory;
use Webmozart\Assert\Assert;

class GenerateCommand extends Command
{
    /**
     * The name and signature of the commands command.
     *
     * @var string
     */
    protected $signature = 'apidocs:generate';

    /**
     * The commands command description.
     *
     * @var string
     */
    protected $description = 'Generates API documentation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the commands command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(__('apidocs::console.begin'));

        // Fetch routes
        $routes = $this->getRoutes();
        $this->info(__('apidocs::console.routes', ['count' => $routes->count()]));

        // Inject route docblock(s)
        $this->injectDocBlocks($routes);

        // Perform route preflight checks
        $routes = $this->preflightRoutes($routes);

        // Mutate routes
        $this->mutateRoutes($routes);

        // Group routes and sort alphabetically
        $routeGroups = $routes->groupBy(config('apidocs.groupBy', 'meta.groups.*.title'));
        $routeGroups = $routeGroups->sortKeys();

        $this->info(__('apidocs::console.preprocess', [
            'groups' => $routeGroups->count(),
            'routes' => $routes->count()
        ]));

        // Process route groups
        $digest = $this->processRouteGroups($routeGroups);

        // Generation finished
        $this->info(__('apidocs::console.finish', [
            'groups' => $routeGroups->count(),
            'routes' => $routes->count()
        ]));
        $this->table(__('apidocs::console.process.table'), $digest);
        return null;
    }

    /**
     * Fetches the routes for API documentation generation and wraps it
     * in a documented route ({@see DocRoute}).
     *
     * @return Collection<DocRoute> filtered documented route instances
     */
    private function getRoutes(): Collection
    {
        ['matches' => $matches, 'hides' => $hides] = config('apidocs.routes');
        return RouteHelper::getRoutes($matches, $hides)->mapInto(DocRoute::class);
    }

    /**
     * Performs route preflight checks to determine suitability for API
     * documentation generation, and hence filters valid routes.
     *
     * @param Collection<DocRoute> $routes matched route instances
     * @return Collection<DocRoute> route instances suitable for documentation
     */
    private function preflightRoutes(Collection $routes): Collection
    {
        $checks = config('apidocs.preflight', []);
        Assert::allIsAOf($checks, RoutePreflight::class, 'Expected route preflight to be an instance of %2$s. Got: %s');

        return $routes->filter(function ($route) use ($checks) {
            /** @var DocRoute $route */
            $methods = implode(',', $route->methods());
            $uri = $route->uri();

            foreach ($checks as $check) {
                if ($reason = call_user_func([$check, 'apply'], $route)) {
                    $degree = call_user_func([$check, 'degree']);
                    Assert::oneOf($degree, PreflightDegree::values(), 'Expected a logging degree of: %2$s. Got: %s');
                    $this->{$degree}(__('apidocs::console.preflight', compact('methods', 'uri', 'reason')));
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Injects docblock(s) for the given routes (or null if not specified).
     *
     * @param Collection<DocRoute> $routes matched route instances
     */
    private function injectDocBlocks(Collection $routes): void
    {
        $tags = config('apidocs.tags', []);
        Assert::allIsAOf(array_values($tags), Tag::class, 'Expected DocBlock tag to be an instance of %2$s. Got: %s');
        $factory = DocBlockFactory::createInstance($tags);

        $routes->each(function ($route) use ($factory) {
            /** @var DocRoute $route */
            $route->addDocBlock('controller', RouteHelper::getControllerDocBlock($route->getRoute(), $factory));
            $route->addDocBlock('action', RouteHelper::getActionDocBlock($route->getRoute(), $factory));
        });
    }

    /**
     * Mutates route instances prior to documentation generation.
     *
     * @param Collection<DocRoute> $routes checked route instances
     */
    private function mutateRoutes(Collection $routes): void
    {
        $mutators = config('apidocs.mutators', []);
        Assert::allIsAOf($mutators, RouteMutator::class, 'Expected route mutator to be an instance of %2$s. Got: %s');

        $routes->each(function ($route) use ($mutators) {
            foreach ($mutators as $mutator)
                call_user_func([$mutator, 'mutate'], $route);
        });
    }

    /**
     * Processes route groups for documentation generation.
     *
     * @param Collection $routeGroups
     * @return array processed routes information (e.g. file path, size, etc.)
     */
    private function processRouteGroups(Collection $routeGroups): array
    {
        /** @var string $output documentation output filename format */
        $output = config('apidocs.output', 'docs/:name.md');

        // Prepare progress bar
        $progress = $this->output->createProgressBar($routeGroups->count());
        $progress->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s% • %message%');
        $progress->setMessage('...');

        // Prepare output table
        $table = [];

        // Process route groups
        foreach ($routeGroups as $groupName => $routes) {
            /** @var Collection<DocRoute> $routes */

            // Compute route group properties
            // TODO: Replace the version in the output path
            $path = str_replace([':version', ':name'], ['1.0', Str::slug($groupName)], $output);
            $count = $routes->count();

            // Update progress bar
            $progress->setMessage(__('apidocs::console.process.bar', [
                'name'  => $groupName,
                'count' => $count,
                'path'  => $path
            ]));

            // Render and persist to disk
            try {
                view('apidocs::page')->with([
                    'groupName' => $groupName,
                    'routes'    => $routes,
                    'path'      => $path
                ])->render(function ($view, $content) use ($groupName, $count, $path, &$table) {
                    // Persist rendered content to disk
                    File::ensureDirectoryExists(File::dirname($path));
                    $size = File::put($path, $content);

                    // Add route group to console table
                    $table[] = [
                        'group'  => $groupName,
                        'routes' => $count,
                        'path'   => $path,
                        'size'   => self::bytesToHuman($size)
                    ];
                });
            } catch (\Throwable $e) {
                $progress->clear();
                $this->error(__('apidocs::console.process.error', [
                    'name'  => $groupName,
                    'count' => $count,
                    'path'  => $path,
                    'error' => $e->getMessage()
                ]));
            }

            $progress->advance();
        }

        // Finish processing
        $progress->finish();
        $this->info(''); // NB: New line after progress bar
        return $table;
    }

    /**
     * Computes and formats given bytes to human readable format.
     *
     * @param int $bytes     number of bytes
     * @param int $precision number of decimal points
     * @return string human readable file size
     */
    public static function bytesToHuman(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
        for ($i = 0; $bytes > 1024; $i++)
            $bytes /= 1024;
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
