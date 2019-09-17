<?php

namespace InetStudio\ArticlesPackage\Articles\Http\Responses\Front\Export;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use InetStudio\ArticlesPackage\Articles\Contracts\Exports\CommentsExportContract;
use InetStudio\ArticlesPackage\Articles\Contracts\Http\Responses\Front\Export\CommentsExportResponseContract;

/**
 * Class CommentsExportResponse.
 */
class CommentsExportResponse implements CommentsExportResponseContract
{
    /**
     * @var CommentsExportContract
     */
    protected $export;

    /**
     * CommentsExportResponse constructor.
     *
     * @param  CommentsExportContract  $export
     */
    public function __construct(CommentsExportContract $export)
    {
        $this->export = $export;
    }

    /**
     * Экспорт данных.
     *
     * @param  Request  $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        $data = [
            'route' => $request->route()->parameters(),
            'request' => $request->all(),
        ];

        $this->export->setData($data);

        return Excel::download($this->export, time().'.xlsx');
    }
}
