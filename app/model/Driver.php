<?php


namespace app\model;

use think\facade\Cache;
use think\Model;

/**
 * Admin
 * created on 2021/7/9 17:24
 * created by chengzhigang
 */
class Driver extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin_driverinfo';
    protected $autoWriteTimestamp = 'datetime';



}
