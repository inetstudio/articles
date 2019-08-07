<?php

namespace InetStudio\ArticlesPackage\Console\Commands;

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
    protected $name = 'inetstudio:articles-package:setup';

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
                'description' => 'Statuses setup',
                'command' => 'inetstudio:articles-package:articles:setup',
            ],
        ];
    }
}
