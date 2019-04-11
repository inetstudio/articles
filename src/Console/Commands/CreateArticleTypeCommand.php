<?php

namespace InetStudio\Articles\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateArticleTypeCommand.
 */
class CreateArticleTypeCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:articles:type';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Create classifiers article type';

    /**
     * Запуск команды.
     *
     * @return void
     */
    public function handle(): void
    {
        $groupsService = app()->make('InetStudio\Classifiers\Groups\Contracts\Services\Back\ItemsServiceContract');

        if (DB::table('classifiers_entries')->where('alias', 'material_type_article')->count() == 0) {
            $now = Carbon::now()->format('Y-m-d H:m:s');

            $group = $groupsService->getModel()::updateOrCreate([
                'name' => 'Тип материала',
            ], [
                'alias' => 'article_material_types',
            ]);

            $id = DB::connection('mysql')->table('classifiers_entries')->insertGetId([
                'value' => 'Статья',
                'alias' => 'material_type_article',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $group->entries()->attach($id);
        }
    }
}
