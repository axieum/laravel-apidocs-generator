<?php

namespace Axieum\ApiDocs\Commands;

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

        // Prepare progress bar
        $progress = $this->output->createProgressBar(/* max routes */ 1000000);
        $progress->setRedrawFrequency(100);

        $progress->start();
        for ($i = 0; $i < $progress->getMaxSteps(); $i++)
            $progress->advance();
        $progress->finish();

        $this->info(PHP_EOL . 'Successfully generated API documentation!');

        return null;
    }
}
