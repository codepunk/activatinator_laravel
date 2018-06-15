<?php

namespace Codepunk\Activatinator\Console;

use Illuminate\Console\Command;

class ClearActivationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activatinator:clear-activations {name? : The name of the activatinator broker}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush expired activation tokens';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->laravel['codepunk.activatinator']->broker($this->argument('name'))->getRepository()->deleteExpired();

        $this->info('Expired activation tokens cleared!');
    }
}

