@extends('admin::layouts.app')

@php
    $title = ($item->id) ? 'Редактирование статьи' : 'Добавление статьи';
@endphp

@section('title', $title)

@pushonce('styles:jstree')
    <!-- JSTREE -->
    <link href="{!! asset('admin/css/plugins/jstree/style.min.css') !!}" rel="stylesheet">
@endpushonce

@pushonce('styles:datatables')
    <!-- DATATABLES -->
    <link href="{!! asset('admin/css/plugins/datatables/datatables.min.css') !!}" rel="stylesheet">
@endpushonce

@pushonce('styles:articles_custom')
    <!-- CUSTOM STYLE -->
    <link href="{!! asset('admin/css/modules/products/custom.css') !!}" rel="stylesheet">
@endpushonce

@section('content')

    @push('breadcrumbs')
        @include('admin.module.articles::partials.breadcrumbs')
        <li>
            <a href="{{ route('back.articles.index') }}">Статьи</a>
        </li>
    @endpush

    @if ($item->id)
        <div class="row m-sm">
            <a class="btn btn-white" href="{{ $item->href }}" target="_blank">
                <i class="fa fa-eye"></i> Посмотреть на сайте
            </a>
        </div>
    @endif

    <div class="wrapper wrapper-content">

        {!! Form::info() !!}

        {!! Form::open(['url' => (!$item->id) ? route('back.articles.store') : route('back.articles.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

            @if ($item->id)
                {{ method_field('PUT') }}
            @endif

            {!! Form::hidden('article_id', (!$item->id) ? '' : $item->id) !!}

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

                                    {!! Form::string('title', $item->title, [
                                        'label' => [
                                            'title' => 'Заголовок',
                                        ],
                                        'field' => [
                                            'class' => 'form-control slugify',
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
                                            'src' => isset($previewImageMedia) ? url($previewImageMedia->getUrl()) : '',
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
                                                    'description' => 'Минимальный размер области 3x4 — 768x512 пикселей'
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

                                    {!! Form::dropdown('ingredients[]', $item->ingredients()->pluck('id')->toArray(), [
                                        'label' => [
                                            'title' => 'Ингредиенты',
                                        ],
                                        'field' => [
                                            'class' => 'select2 form-control',
                                            'data-placeholder' => 'Выберите ингредиенты',
                                            'style' => 'width: 100%',
                                            'multiple' => 'multiple',
                                            'data-source' => route('back.ingredients.getSuggestions'),
                                        ],
                                        'options' => (old('ingredients')) ? \InetStudio\Ingredients\Models\IngredientModel::whereIn('id', old('ingredients'))->pluck('title', 'id')->toArray() : $item->ingredients()->pluck('title', 'id')->toArray(),
                                    ]) !!}

                                    <div class="form-group ">
                                        <label for="title" class="col-sm-2 control-label">Категории</label>

                                        <div class="col-sm-10">
                                            @if (count($categories) > 0)
                                                <div class="jstree-list" data-target="categories" data-multiple="true" data-cascade="up">
                                                    <ul>
                                                        @foreach ($categories as $category)
                                                            @include('admin.module.categories::pages.categories.partials.form_category', [
                                                                'id' => 'сategoryId',
                                                                'item' => $category,
                                                                'currentId' => null,
                                                                'selected' => $item->categories()->pluck('id')->toArray(),
                                                            ])
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <p>Список категорий пуст.</p>
                                            @endif

                                            {!! Form::hidden('categories', '') !!}

                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>

                                    {!! Form::dropdown('tags[]', $item->tags()->pluck('id')->toArray(), [
                                        'label' => [
                                            'title' => 'Теги',
                                        ],
                                        'field' => [
                                            'class' => 'select2 form-control',
                                            'data-placeholder' => 'Выберите теги',
                                            'style' => 'width: 100%',
                                            'multiple' => 'multiple',
                                            'data-source' => route('back.tags.getSuggestions'),
                                        ],
                                        'options' => (old('tags')) ? \InetStudio\Tags\Models\TagModel::whereIn('id', old('tags'))->pluck('name', 'id')->toArray() : $item->tags()->pluck('name', 'id')->toArray(),
                                    ]) !!}

                                    {!! Form::datepicker('publish_date', ($item->publish_date) ? date('d.m.Y H:i', strtotime($item->publish_date)) : '', [
                                        'label' => [
                                            'title' => 'Дата публикации',
                                        ],
                                        'field' => [
                                            'class' => 'datetimepicker form-control',
                                        ],
                                    ]) !!}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::products('products', $item->products, ['table' => $productsTable])!!}

            {!! Form::buttons('', '', ['back' => 'back.articles.index']) !!}

        {!! Form::close()!!}
    </div>
@endsection

@pushonce('scripts:jstree')
    <!-- JSTREE -->
    <script src="{!! asset('admin/js/plugins/jstree/jstree.min.js') !!}"></script>
@endpushonce

@pushonce('scripts:datatables')
    <!-- DATATABLES -->
    <script src="{!! asset('admin/js/plugins/datatables/datatables.min.js') !!}"></script>
@endpushonce

@pushonce('scripts:datatable_products_embedded')
    {!! $productsTable->scripts() !!}
@endpushonce

@pushonce('scripts:articles_custom')
    <!-- CUSTOM SCRIPT -->
    <script src="{!! asset('admin/js/modules/products/custom.js') !!}"></script>
@endpushonce
