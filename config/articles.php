<?php

return [

    /*
     * Настройки изображений
     */

    'images' => [
        'quality' => 75,
        'conversions' => [
            'og_image' => [
                [
                    'name' => 'og_image_default',
                    'size' => [
                        'width' => 968,
                        'height' => 475,
                    ],
                ],
            ],
            'preview' => [
                [
                    'name' => 'preview_3_2',
                    'size' => [
                        'width' => 768,
                        'height' => 512,
                    ],
                ],
                [
                    'name' => 'preview_3_4',
                    'size' => [
                        'width' => 384,
                        'height' => 512,
                    ],
                ],
            ],
        ]
    ],
];
