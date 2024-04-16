<?php
/**
 *redis.php
 *文件描述
 *Created on 2021/12/16 11:10
 *Created by chengzhigang
 */
use think\facade\Env;
return [
    'options' => [
        'prefix' => Env::get('REDIS_PREFIX',Env::get('APP_NAME').'_'),
    ],
    'connections' => [
        'default' => [
            'host' => Env::get('REDIS_HOST','127.0.0.1'),
            'port' => Env::get('REDIS_PORT',6379),
            'password' => Env::get('REDIS_PASSWORD',''),
            'database' => Env::get('REDIS_DATABASE',3),
        ]
    ]
];
