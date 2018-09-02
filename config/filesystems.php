<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальные диски для хранения изображений постов и пользователей
     */

    'articles' => [
        'driver' => 'local',
        'root' => storage_path('app/public/articles'),
        'url' => env('APP_URL').'/storage/articles',
        'visibility' => 'public',
    ],

];
