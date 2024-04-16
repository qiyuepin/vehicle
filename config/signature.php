<?php
/**
 *signature.php
 *文件描述
 *Created on 2021/10/27 10:18
 *Created by chengzhigang
 */
use think\facade\Env;

return [
    'switch' => Env::get('SIGNATURE_SWITCH',true),//签名开关
    'expire' => Env::get('SIGNATURE_EXPIRE',60), //请求有效时间
    'type' => Env::get('SIGNATURE_TYPE','MD5'),//加密方式
    'white_list' => [
        'admin/getCaptcha',
        'admin/uploadImage',
        'admin/uploadFile',
        'admin/readFile',
        'admin/gitPull',
        'admin/queueInfo',
        'admin/queueRetry',
        'admin/queueDelete',
        'admin/index/ceshi',
        'admin/admin/getList',
        'admin/admin/getRole',
        'admin/auth/getRules',
        'admin/user/info',
        'admin/admin/getdriverList',
        'admin/admin/adddriverAdmin',
        'admin/admin/addAdmin',
        'admin/admin/getdriverInfo',
        'admin/admin/editdriverAdmin',
        'admin/driver/getregulation',
        'admin/driver/addregulation',
        'admin/driver/getaccident',
        'admin/driver/addaccident',
        'admin/driver/test',
        'admin/auth/freshToken'
    ],//白名单
];
