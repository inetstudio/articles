<?php

namespace InetStudio\Articles\Commands;

use Illuminate\Console\Command;

class CreateFoldersCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'inetstudio:articles:folders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create package folders';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (config('filesystems.disks.articles')) {
            $path = config('filesystems.disks.articles.root');

            if (! is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }
    }
}
