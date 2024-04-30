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
        'admin/info/getescort',
        'admin/info/addescort',
        'admin/info/editescort',
        'admin/info/getescortInfo',
        'admin/info/getinfolist',
        'admin/info/addinfo',
        'admin/info/editinfo',
        'admin/info/getinfo',
        'admin/info/getcarlist',
        'admin/info/getfactory',
        'admin/info/getfactoryinfo',
        'admin/info/addfactory',
        'admin/info/editfactory',
        'admin/info/delfactory',
        'admin/info/getcartrailerInfo',
        'admin/plan/getnormal',
        'admin/plan/addnormal',
        'admin/plan/editnormal',
        'admin/plan/getnormalinfo',
        'admin/plan/gettemporary',
        'admin/plan/addtemporary',
        'admin/plan/edittemporary',
        'admin/plan/gettemporaryinfo',
        'admin/plan/getplaninfo',
        'admin/plan/getplans',
        'admin/plan/addplan',
        'admin/plan/editplan',
        'admin/plan/distplan',
        'admin/plan/getplansinfo',
        'admin/plan/driver_normal',
        'admin/plan/driver_sumitnormal',
        'admin/auth/freshToken'
    ],//白名单
];
