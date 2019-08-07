<?php

namespace InetStudio\ArticlesPackage\Articles\Console\Commands;

use InetStudio\AdminPanel\Base\Console\Commands\BaseSetupCommand;

/**
 * Class SetupCommand.
 */
class SetupCommand extends BaseSetupCommand
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:articles-package:articles:setup';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Setup articles package';

    /**
     * Инициализация команд.
     */
    protected function initCommands(): void
    {
        $this->calls = [
            [
                'type' => 'artisan',
                'description' => 'Publish migrations',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\ArticlesPackage\Articles\Providers\ServiceProvider',
                    '--tag' => 'migrations',
                ],
            ],
            [
                'type' => 'artisan',
                'description' => 'Migration',
                'command' => 'migrate',
            ],
            [
                'type' => 'artisan',
                'description' => 'Create folders',
                'command' => 'inetstudio:articles-package:articles:folders',
            ],
            [
                'type' => 'artisan',
                'description' => 'Create classifiers article type',
                'command' => 'inetstudio:articles-package:articles:type',
            ],
            [
                'type' => 'artisan',
                'description' => 'Publish config',
                'command' => 'vendor:publish',
                'params' => [
                    '--provider' => 'InetStudio\ArticlesPackage\Articles\Providers\ServiceProvider',
                    '--tag' => 'config',
                ],
            ],
        ];
    }
}
