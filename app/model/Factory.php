<?php


namespace app\model;

use think\facade\Cache;
use think\Model;


class Factory extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin_factory';
    protected $autoWriteTimestamp = 'datetime';

}
