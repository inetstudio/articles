@php
    $typeTitles = [
        'article' => 'Статья',
    ];
@endphp

@if ($type)
    <span class="label label-default">{{ isset($typeTitles[$type]) ? $typeTitles[$type] : 'Статья' }}</span>
@endif
