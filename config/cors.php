<?php
/**
 *cors.php
 *文件描述
 *Created on 2020/11/9 10:37
 *Created by chengzhigang
 */
return [
    'header' => [
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Max-Age'           => 1800,
        'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With, X-Access-Appid',
        'Access-Control-Allow-Origin' => '*',
    ]
];
