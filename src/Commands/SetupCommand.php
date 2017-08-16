<?php

namespace InetStudio\Articles\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'inetstudio:articles:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup articles package';

    /**
     * Commands to call with their description.
     *
     * @var array
     */
    protected $calls = [
        'vendor:publish --provider="InetStudio\Articles\ArticlesServiceProvider" --tag="migrations"' => 'Publish migrations',
        'migrate' => 'Migration',
        'optimize' => 'Optimize',
        'inetstudio:articles:folders' => 'Create folders',
        'vendor:publish --provider="InetStudio\Articles\ArticlesServiceProvider" --tag="public" --force' => 'Publish public',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        foreach ($this->calls as $command => $info) {
            $this->line(PHP_EOL.$info);
            $this->call($command);
        }
    }
}
