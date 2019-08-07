@pushonce('modals:choose_article')
    <div id="choose_article_modal" tabindex="-1" role="dialog" aria-hidden="true" class="modal inmodal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h1 class="modal-title">Выберите статью</h1>
                </div>

                <div class="modal-body">
                    <div class="ibox-content">
                        {!! Form::hidden('article_data', '', [
                            'class' => 'choose-data',
                            'id' => 'article_data',
                        ]) !!}

                        {!! Form::string('article', '', [
                            'label' => [
                                'title' => 'Статья',
                            ],
                            'field' => [
                                'class' => 'form-control autocomplete',
                                'data-search' => route('back.articles.getSuggestions'),
                                'data-target' => '#article_data'
                            ],
                        ]) !!}
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Закрыть</button>
                    <a href="#" class="btn btn-primary save">Сохранить</a>
                </div>

            </div>
        </div>
    </div>
@endpushonce
