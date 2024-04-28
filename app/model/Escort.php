<?php


namespace app\model;

use think\facade\Cache;
use think\Model;


class Escort extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin_carescort';
    protected $autoWriteTimestamp = 'datetime';



}
