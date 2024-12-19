<?php


namespace app\model;

use think\facade\Cache;
use think\Model;
use think\model\concern\SoftDelete;


class Plans extends Model
{
    // 设置数据表（不含前缀）
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $name = 'admin_carplans';
    protected $autoWriteTimestamp = 'datetime';

}
