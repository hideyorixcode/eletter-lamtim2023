<?php
return [
    'bookmarkicons' => [
//        ['url' => '#', 'icon' => 'check-square', 'name' => 'Menu']
    ],
    'main' => [
        [
            'name' => 'Topik',
            'url' => '#',
            'icon' => 'slack',
            'sub' => [
                [
                    'name' => 'Kemiskinan',
                    'url' => 'topik/kemiskinan',
                    'icon' => 'shopping-bag',
                    'sub' => []
                ],
                [
                    'name' => 'Ekonomi',
                    'url' => 'topik/ekonomi',
                    'icon' => 'pie-chart',
                    'sub' => []
                ]
            ]
        ],
        [
            'name' => 'Dataset',
            'url' => 'dataset',
            'icon' => 'layers',
            'sub' => []
        ],
        [
            'name' => 'Organisasi',
            'url' => 'organisasi',
            'icon' => 'layout',
            'sub' => []
        ],
        [
            'name' => 'Berita',
            'url' => 'post',
            'icon' => 'book-open',
            'sub' => []
        ],
        [
            'name' => 'Bantuan',
            'url' => 'bantuan',
            'icon' => 'help-circle',
            'sub' => []
        ]
    ]
];
