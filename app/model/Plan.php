<?php


namespace app\model;

use think\facade\Cache;
use think\Model;


class Plan extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin_carplan';
    protected $autoWriteTimestamp = 'datetime';

}
