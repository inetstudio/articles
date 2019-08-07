@if ($status)
    <span class="label label-{{ $status->color_class }}">{{ $status->name }}</span>
@endif
