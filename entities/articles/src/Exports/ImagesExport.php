<?php

namespace InetStudio\ArticlesPackage\Articles\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\ArticlesPackage\Articles\Contracts\Exports\ImagesExportContract;

/**
 * Class ImagesExport.
 */
class ImagesExport implements ImagesExportContract, FromCollection, WithMapping, WithHeadings, WithColumnFormatting
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
            'relations' => ['media']
        ];

        return $articlesService->getItemBySlug($this->slug, $params)->first()->media;
    }

    /**
     * @param $media
     *
     * @return array
     */
    public function map($media): array
    {
        return [
            $media->id,
            $media->collection_name,
            $media->getCustomProperty('alt'),
            $media->getCustomProperty('description'),
            $media->getCustomProperty('copyright'),
            url($media->getFullUrl()),
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'collection',
            'alt',
            'description',
            'copyright',
            'url',
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
