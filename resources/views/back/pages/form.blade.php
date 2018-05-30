@extends('admin::back.layouts.app')

@php
    $title = ($item->id) ? 'Редактирование статьи' : 'Добавление статьи';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.articles::back.partials.breadcrumbs.form')
    @endpush

    <div class="row m-sm">
        <a class="btn btn-white" href="{{ route('back.articles.index') }}">
            <i class="fa fa-arrow-left"></i> Вернуться назад
        </a>
        @if ($item->id && $item->href)
            <a class="btn btn-white" href="{{ $item->href }}" target="_blank">
                <i class="fa fa-eye"></i> Посмотреть на сайте
            </a>
        @endif
        @php
            $status = (! $item->id or ! $item->status) ? \InetStudio\Statuses\Models\StatusModel::get()->first() : $item->status;
        @endphp
        <div class="bg-{{ $status->color_class }} p-xs b-r-sm pull-right">{{ $status->name }}</div>
    </div>

    <div class="wrapper wrapper-content">
        {!! Form::info() !!}

        {!! Form::open(['url' => (! $item->id) ? route('back.articles.store') : route('back.articles.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

            @if ($item->id)
                {{ method_field('PUT') }}
            @endif

            {!! Form::hidden('article_id', (! $item->id) ? '' : $item->id, ['id' => 'object-id']) !!}

            {!! Form::hidden('article_type', get_class($item), ['id' => 'object-type']) !!}

            {!! Form::buttons('', '', ['back' => 'back.articles.index']) !!}

            {!! Form::meta('', $item) !!}

            {!! Form::social_meta('', $item) !!}

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-group float-e-margins" id="mainAccordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseMain" aria-expanded="true">Основная информация</a>
                                </h5>
                            </div>
                            <div id="collapseMain" class="panel-collapse collapse in" aria-expanded="true">
                                <div class="panel-body">

                                    {!! Form::classifiers('', $item, [
                                        'label' => [
                                            'title' => 'Тип материала',
                                        ],
                                        'field' => [
                                            'placeholder' => 'Выберите типы материала',
                                            'type' => 'Тип материала',
                                        ],
                                    ]) !!}

                                    {!! Form::string('title', $item->title, [
                                        'label' => [
                                            'title' => 'Заголовок',
                                        ],
                                        'field' => [
                                            'class' => 'form-control '.(($status->classifiers->contains('alias', 'display_for_users')) ? '' : 'slugify'),
                                            'data-slug-url' => route('back.articles.getSlug'),
                                            'data-slug-target' => 'slug',
                                        ],
                                    ]) !!}

                                    {!! Form::string('slug', $item->slug, [
                                        'label' => [
                                            'title' => 'URL',
                                        ],
                                        'field' => [
                                            'class' => 'form-control slugify',
                                            'data-slug-url' => route('back.articles.getSlug'),
                                            'data-slug-target' => 'slug',
                                        ],
                                    ]) !!}

                                    @php
                                        $previewImageMedia = $item->getFirstMedia('preview');
                                    @endphp

                                    {!! Form::crop('preview', $previewImageMedia, [
                                        'label' => [
                                            'title' => 'Превью',
                                        ],
                                        'image' => [
                                            'filepath' => isset($previewImageMedia) ? url($previewImageMedia->getUrl()) : '',
                                            'filename' => isset($previewImageMedia) ? $previewImageMedia->file_name : '',
                                        ],
                                        'crops' => [
                                            [
                                                'title' => 'Размер 3х4',
                                                'name' => '3_4',
                                                'ratio' => '3/4',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('crop.3_4') : '',
                                                'size' => [
                                                    'width' => 384,
                                                    'height' => 512,
                                                    'type' => 'min',
                                                    'description' => 'Минимальный размер области 3x4 — 384x512 пикселей'
                                                ],
                                            ],
                                            [
                                                'title' => 'Размер 3х2',
                                                'name' => '3_2',
                                                'ratio' => '3/2',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('crop.3_2') : '',
                                                'size' => [
                                                    'width' => 768,
                                                    'height' => 512,
                                                    'type' => 'min',
                                                    'description' => 'Минимальный размер области 3x2 — 768x512 пикселей'
                                                ],
                                            ],
                                        ],
                                        'additional' => [
                                            [
                                                'title' => 'Описание',
                                                'name' => 'description',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('description') : '',
                                            ],
                                            [
                                                'title' => 'Copyright',
                                                'name' => 'copyright',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('copyright') : '',
                                            ],
                                            [
                                                'title' => 'Alt',
                                                'name' => 'alt',
                                                'value' => isset($previewImageMedia) ? $previewImageMedia->getCustomProperty('alt') : '',
                                            ],
                                        ],
                                    ]) !!}

                                    {!! Form::wysiwyg('description', $item->description, [
                                        'label' => [
                                            'title' => 'Лид',
                                        ],
                                        'field' => [
                                            'class' => 'tinymce-simple',
                                            'type' => 'simple',
                                            'id' => 'description',
                                        ],
                                    ]) !!}

                                    {!! Form::wysiwyg('content', $item->content, [
                                        'label' => [
                                            'title' => 'Содержимое',
                                        ],
                                        'field' => [
                                            'class' => 'tinymce',
                                            'id' => 'content',
                                            'hasImages' => true,
                                        ],
                                        'images' => [
                                            'media' => $item->getMedia('content'),
                                            'fields' => [
                                                [
                                                    'title' => 'Описание',
                                                    'name' => 'description',
                                                ],
                                                [
                                                    'title' => 'Copyright',
                                                    'name' => 'copyright',
                                                ],
                                                [
                                                    'title' => 'Alt',
                                                    'name' => 'alt',
                                                ],
                                            ],
                                        ],
                                    ]) !!}

                                    {!! Form::widgets('', $item) !!}

                                    {!! Form::ingredients('', $item) !!}

                                    {!! Form::categories('', $item) !!}

                                    {!! Form::tags('', $item) !!}

                                    {!! Form::classifiers('', $item, [
                                        'label' => [
                                            'title' => 'Тип кожи',
                                        ],
                                        'field' => [
                                            'placeholder' => 'Выберите типы кожи',
                                            'type' => 'Тип кожи',
                                        ],
                                    ]) !!}

                                    {!! Form::datepicker('publish_date', ($item->publish_date) ? $item->publish_date->fornat('d.m.Y H:i') : '', [
                                        'label' => [
                                            'title' => 'Дата публикации',
                                        ],
                                        'field' => [
                                            'class' => 'datetimepicker form-control',
                                        ],
                                    ]) !!}

                                    {!! Form::status('', $item) !!}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::products('products', $item->products)!!}

            {!! Form::access('articles', $item) !!}

            <hr/>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-group float-e-margins" id="correctionsAccordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#correctionsAccordion" href="#collapseCorrections" aria-expanded="false" class="collapsed">Доработки</a>
                                </h5>
                            </div>
                            <div id="collapseCorrections" class="panel-collapse collapse" aria-expanded="false">
                                <div class="panel-body">
                                    {!! Form::wysiwyg('corrections', $item->corrections, [
                                        'label' => [
                                            'title' => 'Доработки',
                                        ],
                                        'field' => [
                                            'class' => 'tinymce',
                                            'id' => 'corrections',
                                            'hasImages' => true,
                                        ],
                                        'images' => [
                                            'media' => $item->getMedia('corrections'),
                                            'fields' => [
                                                [
                                                    'title' => 'Описание',
                                                    'name' => 'description',
                                                ],
                                                [
                                                    'title' => 'Alt',
                                                    'name' => 'alt',
                                                ],
                                            ]
                                        ],
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::buttons('', '', ['back' => 'back.articles.index']) !!}

        {!! Form::close()!!}
    </div>

    @include('admin.module.articles::back.pages.modals.suggestion')
    @include('admin.module.ingredients::back.pages.modals.suggestion')
    @include('admin.module.persons::back.pages.modals.suggestion')
    @include('admin.module.products::back.pages.modals.suggestion')
    @include('admin.module.polls::back.pages.modals.form')
    @include('admin.module.reviews.messages::back.widgets.messages_list')
    @include('admin.module.quizzes::back.pages.modals.suggestion')
@endsection
