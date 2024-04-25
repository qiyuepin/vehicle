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
        'admin/driver/getregulationinfo',
        'admin/driver/addregulation',
        'admin/driver/editregulation',
        'admin/driver/delregulation',
        'admin/driver/getaccident',
        'admin/driver/addaccident',
        'admin/driver/delaccident',
        'admin/driver/test',
        'admin/user/logout',
        'admin/info/getcarscope',
        'admin/info/getcarhead',
        'admin/info/addcarhead',
        'admin/info/editcarhead',
        'admin/info/getcarheadInfo',
        'admin/info/getcarscope',
        'admin/info/getcartrailer',
        'admin/info/addcartrailer',
        'admin/info/editcartrailer',
        'admin/info/getcartrailerInfo',
        'admin/auth/freshToken'
    ],//白名单
];
