<?php

return [
    // 默认磁盘
    'default' => env('filesystem.driver', 'local'),
    // 磁盘列表
    'disks'   => [
        'local'  => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            // 磁盘类型
            'type'       => 'local',
            // 磁盘路径
            'root'       => app()->getRootPath() . 'public/storage',
            // 磁盘路径对应的外部URL路径
            'url'        => '/storage',
            // 可见性
            'visibility' => 'public',
        ],
        // 更多的磁盘配置信息
        'qiniu' => [
            'access_key' => env('filesystem.qiniu.access_key','wf9m-S2MApjB3h3585bCQ0I34j8QMdl8Cdq7PK0U'),
            'secret_key' => env('filesystem.qiniu.secret_key','HBs6fxlsSFqsZbaKkXOJLt4GmO7eHuo0WYVtKpdk'),
            'bucket' => env('filesystem.qiniu.bucket','blog'),//文件空间名称
            'domain_name' => env('filesystem.qiniu.domain_name','qiniu.chengzhigang.cn')
        ]
    ],
    'type' => [
        'image' => [
            'size' => 10485760,//1M
            'ext' => 'jpeg,jpg,gif,bmp,png',
            'water' => [
                'status' => false,//默认不开起水印
            ],
            'thumb' => [
                'status' => false,//默认不生成缩略图
                'width' => 100,
                'height' => 100,
                'type' => 1,//等比例缩放
            ],
            'dir' => 'image',//上传目录
        ],
        'doc' => [
            'size' => 10485760,//10M
            'ext' => 'doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip,gz,bz2,7z,js,css,html,php',
            'dir' => 'doc',//上传目录
        ],
        'video' => [
            'size' => 10485760,//10M
            'ext' => 'avi,mov,rmvb,rm,flv,mp4,3gp,f4v,mkv,mpeg,asf,wmv,navi,ogg',
            'dir' => 'video',//上传目录
        ],
        'audio' => [
            'size' => 10485760,//10M
            'ext' => 'mp3',
            'dir' => 'audio',//上传目录
        ]
    ]
];
