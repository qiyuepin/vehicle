<?php


namespace app\model;

use think\facade\Cache;
use think\Model;

/**
 * Advert
 * created on 2021/7/9 17:24
 * created by chengzhigang
 */
class Cost extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin_cost';
    protected $autoWriteTimestamp = 'datetime';


}
