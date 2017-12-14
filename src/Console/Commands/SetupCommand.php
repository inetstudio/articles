<?php

namespace InetStudio\Articles\Console\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:articles:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup articles package';

    /**
     * Список дополнительных команд.
     *
     * @var array
     */
    protected $calls = [];

    /**
     * Запуск команды.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->initCommands();

        foreach ($this->calls as $info) {
            if (! isset($info['command'])) {
                continue;
            }

            $this->line(PHP_EOL.$info['description']);
            $this->call($info['command'], $info['params']);
        }
    }

    /**
     * Инициализация команд.
     *
     * @return void
     */
    private function initCommands(): void
    {
        $this->calls = [
            (! class_exists('CreateLikeCounterTable')) ? [
                'description' => 'Likeable setup',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'Cog\Likeable\Providers\LikeableServiceProvider',
                    '--tag' => 'migrations',
                ],
            ] : [],
            [
                'description' => 'Publish migrations',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\Articles\Providers\ArticlesServiceProvider',
                    '--tag' => 'migrations',
                ],
            ],
            [
                'description' => 'Migration',
                'command' => 'migrate',
                'params' => [],
            ],
            [
                'description' => 'Create folders',
                'command' => 'inetstudio:articles:folders',
                'params' => [],
            ],
            [
                'description' => 'Publish public',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\Articles\Providers\ArticlesServiceProvider',
                    '--tag' => 'public',
                    '--force' => true,
                ],
            ],
            [
                'description' => 'Publish config',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\Articles\Providers\ArticlesServiceProvider',
                    '--tag' => 'config',
                ],
            ],
        ];
    }
}
