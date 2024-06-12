<?php

namespace app\model;

use think\facade\Cache;
use think\Model;

class PlanHistory extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin_carplans_his';
    protected $autoWriteTimestamp = 'datetime';
}