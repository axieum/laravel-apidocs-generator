<?php

namespace Axieum\ApiDocs\Commands;

use Axieum\ApiDocs\util\RouteHelper;
use Illuminate\Console\Command;

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
        $this->info('Generating API documentation...');

        ['rules' => $rules, 'hidden' => $hidden] = config('apidocs.routes');
        $routes = RouteHelper::getRoutes($rules, $hidden);

        $this->info(PHP_EOL . 'Successfully generated API documentation for ' . sizeof($routes) . ' routes!');

        return null;
    }
}
