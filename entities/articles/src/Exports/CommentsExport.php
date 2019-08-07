<?php

namespace InetStudio\ArticlesPackage\Articles\Exports;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\ArticlesPackage\Articles\Contracts\Exports\CommentsExportContract;

/**
 * Class CommentsExport.
 */
class CommentsExport implements CommentsExportContract, FromCollection, WithMapping, WithHeadings, WithColumnFormatting
{
    use Exportable;

    /**
     * @var string
     */
    protected $slug = '';

    /**
     * PointsExport constructor.
     *
     * @param string $slug
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * Получаем данные для экспорта.
     *
     * @return Collection
     *
     * @throws BindingResolutionException
     */
    public function collection()
    {
        $articlesService = app()->make('InetStudio\ArticlesPackage\Articles\Contracts\Services\Front\ItemsServiceContract');

        $params = [
            'relations' => ['comments']
        ];

        return $articlesService->getItemBySlug($this->slug, $params)->first()->comments;
    }

    /**
     * @param $comment
     *
     * @return array
     */
    public function map($comment): array
    {
        $user = $comment->user;

        return [
            $comment->id,
            $user->name ?? $comment->name,
            $user->email ?? $comment->email,
            $comment->message,
            Date::dateTimeToExcel($comment->created_at),
            ($user && $user->subscription) ? Date::dateTimeToExcel($user->subscription->created_at) : null,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'name',
            'email',
            'comment',
            'created_at',
            'subscribed_at',
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_DATE_DATETIME,
            'F' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
