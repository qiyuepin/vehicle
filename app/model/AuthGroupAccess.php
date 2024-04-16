<?php


namespace app\model;

use think\model\Pivot;

/**
 * AuthGroupAccess
 * created on 2021/7/9 17:24
 * created by chengzhigang
 */
class AuthGroupAccess extends Pivot
{
    // 设置数据表（不含前缀）
    protected $name = 'auth_group_access';
    protected $autoWriteTimestamp = 'datetime';

}
