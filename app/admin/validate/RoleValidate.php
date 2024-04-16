<?php


namespace app\admin\validate;

use think\Validate;

/**
 * RoleValidate
 * created on 2021/11/2 18:06
 * created by chengzhigang
 */
class RoleValidate extends Validate
{
    protected $rule =   [
        'page|当前页'  => 'require|number|gt:0',
        'limit|每页数' => 'require|number|gt:0',
        'id|ID' => 'require|number|gt:0',
        'status|状态' => 'require|between:0,1',
        'ids|ID' => 'require|array',
        'title|角色名称' => 'require|max:20',
        'rules|菜单权限' => 'array'
    ];

    protected $message  =   [
    ];

    protected $scene = [
        'getList'  =>  ['page','limit'],
        'getInfo' => ['id'],
        'addRole' => ['title','rules'],
        'editRole' => ['id','title','rules'],
        'changeStatus' => ['id','status'],
        'deleteRole' => ['ids']
    ];

}
