<?php


namespace app\admin\validate;

use think\Validate;

/**
 * ChatValidate
 * created on 2021/12/17 11:25
 * created by chengzhigang
 */
class ChatValidate extends Validate
{

    protected $rule =   [
        'touid|管理员ID'  => 'require|number|gt:0',
        'remark|备注' => 'requirePresent|max:100',
        'account|用户名' => 'require|max:10',
        'id|管理员ID' => 'require|number|gt:0',
        'page|当前页' => 'require|number|gt:0',
        'limit|每页条数' => 'require|number|gt:0',
    ];

    protected $message  =   [
    ];

    protected $scene = [
        'addFriend'  =>  ['touid','remark'],
        'getUser' => ['account'],
        'agreeFriendApply' => ['id','remark'],
        'getHistoryMsg' => ['touid','page','limit']
    ];

    //自定义验证字段必须存在，值可以为空
    protected function requirePresent($value,$rules,$data,$field){
        return array_key_exists($field,$data);
    }
}
