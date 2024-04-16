<?php


namespace app\model;

use think\Model;

/**
 * AuthGroup
 * created on 2021/7/9 17:24
 * created by chengzhigang
 */
class AuthGroup extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'auth_group';
    protected $autoWriteTimestamp = 'datetime';

}
