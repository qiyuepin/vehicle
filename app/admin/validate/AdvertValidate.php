<?php


namespace app\admin\validate;

use think\Validate;

/**
 * AdvertValidate
 * created on 2021/11/2 18:06
 * created by chengzhigang
 */
class AdvertValidate extends Validate
{
    protected $rule =   [
        'page|当前页'  => 'require|number|gt:0',
        'limit|每页数' => 'require|number|gt:0',
        'id|ID' => 'require|number|gt:0',
        'status|状态' => 'require|between:1,2',
        'title|标题' => 'require|max:20',
        'position|广告位' => 'require',
        'sort|排序' => 'require',
        'thumb|图片' => 'require',
        'ids|ID' => 'require|array'
    ];

    protected $message  =   [
    ];

    protected $scene = [
        'list'  =>  ['page','limit'],
        'change' => ['id','status'],
        'add' => ['position','title','sort','thumb'],
        'edit' => ['id','position','title','sort','thumb'],
        'info' => ['id'],
        'delete' => ['ids'],
    ];

}
