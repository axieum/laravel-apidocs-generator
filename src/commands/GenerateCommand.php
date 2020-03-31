<?php

namespace Axieum\ApiDocs\Commands;

use Axieum\ApiDocs\formatter\DocFormatter;
use Axieum\ApiDocs\formatter\MarkdownFormatter;
use Axieum\ApiDocs\mutators\RouteMutator;
use Axieum\ApiDocs\preflight\PreflightDegree;
use Axieum\ApiDocs\preflight\RoutePreflight;
use Axieum\ApiDocs\util\DocRoute;
use Axieum\ApiDocs\util\RouteHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlockFactory;
use Webmozart\Assert\Assert;

class GenerateCommand extends Command
{
    /** @var string The name and signature of the command */
    protected $signature = 'apidocs:generate
                                {versions?* : Configured version(s) to generate}';

    /** @var string the command description */
    protected $description = 'Generates API documentation';

    /**
     * Creates a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handles the command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(__('apidocs::console.begin'));

        // Determine versions to generate documentation for
        $versions = $this->getVersions();

        // Fetch all versioned routes
        $versions = $versions->mapWithKeys(function ($version) {
            // Fetch routes
            $routes = $this->getRoutes($version);
            // Inject route docblock(s)
            $this->injectDocBlocks($routes, $version);
            // Perform route preflight checks
            $routes = $this->preflightRoutes($routes, $version);

            $this->info(__('apidocs::console.routes', ['count' => $routes->count(), 'version' => $version]));
            return [$version => $routes];
        });

        // Mutate routes
        $versions->each(function ($routes, $version) {
            $this->mutateRoutes($routes, $version);
        });

        // Process routes
        $digest = [];
        $versions->each(function ($routes, $version) use (&$digest) {
            /** @var Collection<DocRoute> $routes */

            // Group routes and sort alphabetically
            $groups = $routes->groupBy($this->config_fallback($version, 'groupBy', 'meta.groups.*.title'))
                             ->sortKeys();

            $this->info(__('apidocs::console.preprocess',
                ['groups' => $groups->count(), 'routes' => $routes->count(), 'version' => $version]));

            // Process route groups and capture output
            $result = $this->processRouteGroups($groups, $version);
            $digest = array_merge($digest, $result);

            $this->info(__('apidocs::console.processed',
                ['groups' => $groups->count(), 'routes' => $routes->count(), 'version' => $version]));
        });

        // Print digest table (if has results)
        $this->info(__('apidocs::console.finish', ['groups' => sizeof($digest)]));
        if (!empty($digest))
            $this->table(__('apidocs::console.table'), $digest);
        return null;
    }

    /**
     * Determines and returns the versions to be generated.
     *
     * @return Collection<str> versions to generate
     */
    private function getVersions(): Collection
    {
        $versions = collect(config('apidocs.versions'))->keys();
        $supplied = collect($this->argument('versions'))->unique()->toArray();

        // Default to all versions
        if (empty($supplied)) return $versions;

        // Filter versions to those supplied
        return $versions->filter(function ($version) use ($supplied) {
            return Str::is($supplied, $version);
        });
    }

    /**
     * Fetches the routes for API documentation generation and wraps it
     * in a documented route ({@see DocRoute}).
     *
     * @param mixed $version configuration version
     * @return Collection<DocRoute> filtered documented route instances
     */
    private function getRoutes($version): Collection
    {
        ['matches' => $matches, 'hides' => $hides] = $this->config_merge($version, 'routes');
        return RouteHelper::getRoutes($matches, $hides)
                          ->map(function ($route) use ($version) {
                              return new DocRoute($route, $version);
                          });
    }

    /**
     * Performs route preflight checks to determine suitability for API
     * documentation generation, and hence filters valid routes.
     *
     * @param Collection<DocRoute> $routes  matched route instances
     * @param mixed                $version configuration version
     * @return Collection<DocRoute> route instances suitable for documentation
     */
    private function preflightRoutes(Collection $routes, $version): Collection
    {
        $checks = $this->config_merge($version, 'preflight');
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
     * @param Collection<DocRoute> $routes  matched route instances
     * @param mixed                $version configuration version
     */
    private function injectDocBlocks(Collection $routes, $version): void
    {
        $tags = $this->config_merge($version, 'tags');
        Assert::allIsAOf(array_values($tags), Tag::class, 'Expected DocBlock tag to be an instance of %2$s. Got: %s');
        $factory = DocBlockFactory::createInstance($tags);

        $routes->each(function ($route) use ($factory) {
            /** @var DocRoute $route */
            $route->addDocBlock('controller', RouteHelper::getControllerDocBlock($route->getRoute(), $factory));
            $route->addDocBlock('action', RouteHelper::getActionDocBlock($route->getRoute(), $factory));
            $route->addDocBlock('request', RouteHelper::getRequestDocBlock($route->getRoute(), $factory));
        });
    }

    /**
     * Mutates route instances prior to documentation generation.
     *
     * @param Collection<DocRoute> $routes  checked route instances
     * @param mixed                $version configuration version
     */
    private function mutateRoutes(Collection $routes, $version): void
    {
        $mutators = $this->config_merge($version, 'mutators');
        Assert::allIsAOf($mutators, RouteMutator::class, 'Expected route mutator to be an instance of %2$s. Got: %s');

        $routes->each(function ($route) use ($mutators) {
            foreach ($mutators as $mutator)
                call_user_func([$mutator, 'mutate'], $route);
        });
    }

    /**
     * Processes route groups for documentation generation.
     *
     * @param Collection $routeGroups routes grouped under a key
     * @param mixed      $version     configuration version
     * @return array processed routes information (e.g. file path, size, etc.)
     */
    private function processRouteGroups(Collection $routeGroups, $version): array
    {
        /** @var string $output documentation output filename format */
        $output = $this->config_fallback($version, 'output', 'docs/:name.md');

        /** @var string $formatter documentation content formatter */
        $formatter = $this->config_fallback($version, 'formatter', MarkdownFormatter::class);
        Assert::isAOf($formatter, DocFormatter::class, 'Expected formatter to be an instance of %2$s. Got: %s');

        // Prepare progress bar
        $progress = $this->output->createProgressBar($routeGroups->count());
        $progress->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s% • %message%');
        $progress->setMessage('...');

        // Prepare output table
        $table = [];

        // Process route groups
        foreach ($routeGroups as $key => $routes) {
            /** @var Collection<DocRoute> $routes */

            // Compute route group properties
            $path = str_replace([':version', ':name'], [$version, Str::slug($key)], $output);
            $count = $routes->count();

            // Update progress bar
            $progress->setMessage(__('apidocs::console.process.bar', [
                'name'    => $key,
                'version' => $version,
                'count'   => $count,
                'path'    => $path
            ]));

            // Render and persist to disk
            try {
                view('apidocs::index')->with([
                    'key'     => $key,
                    'version' => $version,
                    'routes'  => $routes,
                    'path'    => $path
                ])->render(function ($view, $content) use ($formatter, $key, $count, $path, &$table) {
                    // Format the content
                    $content = call_user_func([$formatter, 'format'], $content);

                    // Persist rendered content to disk
                    File::ensureDirectoryExists(File::dirname($path));
                    $size = File::put($path, $content);

                    // Add route group to console table
                    $table[] = [
                        'group'  => $key,
                        'routes' => $count,
                        'path'   => $path,
                        'size'   => self::bytesToHuman($size)
                    ];
                });
            } catch (\Throwable $e) {
                $progress->clear();
                $this->error(__('apidocs::console.process.error', [
                    'name'    => $key,
                    'version' => $version,
                    'count'   => $count,
                    'path'    => $path,
                    'error'   => $e->getMessage()
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
     * Retrieves and merges the given versioned configuration key with the root
     * configuration values.
     *
     * @param mixed  $version config version
     * @param string $key     config key
     * @param mixed  $default default value if not set
     * @return array merged config values
     */
    public static function config_merge($version, string $key, $default = []): array
    {
        return array_merge_recursive(
            Arr::wrap(config("apidocs.${key}", [])),
            Arr::wrap(config("apidocs.versions.${version}.${key}", $default))
        );
    }

    /**
     * Attempts to retrieve the version configured value first, falling back to
     * the root configured value, and finally the provided value.
     *
     * @param mixed  $version config version
     * @param string $key     config key
     * @param mixed  $default default value if both, version and root values are not set
     * @return mixed
     */
    public static function config_fallback($version, string $key, $default = null)
    {
        return config("apidocs.versions.${version}.${key}", config("apidocs.${key}", $default));
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
