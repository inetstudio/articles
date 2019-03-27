<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title no-padding no-borders">
                <h3 class="m-b-md">Статья</h3>
                <span class="label label-{{ $item->status->color_class }} float-left m-r">{{ $item->status->name }}</span>
                <div class="btn-group float-right">
                    <a href="{{ $item->href }}" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                    <a href="{{ route('back.articles.edit', [$item->id]) }}" target="_blank" class="btn btn-xs btn-default"><i class="fa fa-pencil-alt"></i></a>
                </div>
            </div>
            <div class="ibox-content" style="padding-bottom: 0px">
                <h5>{{ $item->title }}</h5>
            </div>
        </div>
    </div>
</div>
