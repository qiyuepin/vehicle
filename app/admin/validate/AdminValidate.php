<?php


namespace app\admin\validate;

use think\Validate;

/**
 * AdminValidate
 * created on 2021/11/2 18:06
 * created by chengzhigang
 */
class AdminValidate extends Validate
{
    protected $rule =   [
        'page|当前页'  => 'require|number|gt:0',
        'limit|每页数' => 'require|number|gt:0',
        'id|ID' => 'require|number|gt:0',
        'status|状态' => 'require|between:1,2',
        'group|角色' => 'require|array',
        'username|用户名' => 'require|max:10|min:2|unique:admin',
        // 'nickname|昵称' => 'require|chs|max:20',
        'phone|手机号' => 'require|mobile|unique:admin',
        // 'email|邮箱' => 'require|email|unique:admin',
        'password|登录密码' => 'require|max:18|min:6',
        // 'avatar|头像' => 'require',
        // 'autograph|个人签名' => 'requirePresent|max:100',
        'ids|ID' => 'require|array'
    ];

    protected $message  =   [
    ];

    protected $scene = [
        'getList'  =>  ['page','limit'],
        'changeStatus' => ['id','status'],
        'addAdmin' => ['group','username','nickname','phone','email','password','avatar','autograph'],
        'editAdmin' => ['id','group','username','nickname','phone','email','avatar','autograph'],
        'adddriverAdmin' => ['group','username','nickname','phone','password','avatar','autograph','card_front','card_back','driver_card_front','driver_card_back','cert_front','cert_back','id_card_num','dirver_card_num','cert_card_num','employ_time'],
        'editdriverAdmin' => ['id','group','username','nickname','phone','card_front','card_back','driver_card_front','driver_card_back','cert_front','cert_back','id_card_num','dirver_card_num','cert_card_num','employ_time'],
        'getInfo' => ['id'],
        'getdriverInfo' => ['id'],
        'deleteAdmin' => ['ids'],
        'getregulation' => ['id'],
        'getregulationinfo' => ['id'],
        'addregulation' => ['regulation_place','regulation_truth','regulation_code'],
        'editregulation' => ['regulation_place','regulation_truth','regulation_code'],
        'delregulation' => ['ids'],
        'getaccident' => ['id'],
        'getaccidentinfo' => ['id'],
        'addaccident' => ['accident_place','accident_respons','accident_kind','accident_loss'],
        'editaccident' => ['accident_place','accident_respons','accident_kind','accident_loss'],
        'delaccident' => ['ids'],
        'test' => ['id'],
        'getLoginLog' => ['page','limit'],
        'getHandleLog' => ['page','limit'],
    ];

    //自定义验证字段必须存在，值可以为空
    protected function requirePresent($value,$rules,$data,$field){
        return array_key_exists($field,$data);
    }

}
