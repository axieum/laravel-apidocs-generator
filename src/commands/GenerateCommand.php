<?php

namespace Axieum\ApiDocs\Commands;

use Axieum\ApiDocs\mutators\RouteMutator;
use Axieum\ApiDocs\preflight\PreflightDegree;
use Axieum\ApiDocs\preflight\RoutePreflight;
use Axieum\ApiDocs\util\RouteHelper;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
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
    protected $signature = 'apidocs:generate
                            {--d|dir= : Override output directory}';

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

        // Group routes
        $routeGroups = $routes->groupBy(config('apidocs.groupBy', 'meta.groups'));

        $this->info(__('apidocs::console.preprocess', [
            'groups' => $routeGroups->count(),
            'routes' => $routes->count()
        ]));

        // TODO: Process route groups
        //

        // Generation finished
        $this->info(__('apidocs::console.finish', [
            'groups' => $routeGroups->count(),
            'routes' => $routes->count()
        ]));
        return null;
    }

    /**
     * Fetches the routes for API documentation generation.
     *
     * @return Collection<Route> filtered route instances
     */
    private function getRoutes(): Collection
    {
        ['matches' => $matches, 'hides' => $hides] = config('apidocs.routes');
        return RouteHelper::getRoutes($matches, $hides);
    }

    /**
     * Performs route preflight checks to determine suitability for API
     * documentation generation, and hence filters valid routes.
     *
     * @param Collection $routes matched route instances
     * @return Collection<Route> route instances suitable for documentation
     */
    private function preflightRoutes(Collection $routes): Collection
    {
        $checks = config('apidocs.preflight', []);
        Assert::allIsAOf($checks, RoutePreflight::class);

        return $routes->filter(function ($route) use ($checks) {
            /** @var Route $route */
            $methods = implode(',', array_diff($route->methods(), [Request::METHOD_HEAD]));
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
     * @param Collection $routes matched route instances
     */
    private function injectDocBlocks(Collection $routes): void
    {
        $tags = config('apidocs.tags', []);
        Assert::allIsAOf(array_values($tags), Tag::class);
        $factory = DocBlockFactory::createInstance($tags);

        $routes->each(function ($route) use ($factory) {
            $route->controllerDocBlock = RouteHelper::getControllerDocBlock($route, $factory);
            $route->actionDocBlock = RouteHelper::getActionDocBlock($route, $factory);
        });
    }

    /**
     * Mutates route instances prior to documentation generation.
     *
     * @param Collection $routes checked route instances
     */
    private function mutateRoutes(Collection $routes): void
    {
        $mutators = config('apidocs.mutators', []);
        Assert::allIsAOf($mutators, RouteMutator::class);

        $routes->each(function ($route) use ($mutators) {
            $route->meta = [];
            foreach ($mutators as $mutator)
                call_user_func([$mutator, 'mutate'], $route);
        });
    }
}
