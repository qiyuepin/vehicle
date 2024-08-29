<?php


namespace app\model;

use think\facade\Cache;
use think\Model;
use think\model\concern\SoftDelete;


class Carhead extends Model
{
    use SoftDelete;
    // 设置数据表（不含前缀）
    protected $name = 'admin_carhead';
    protected $autoWriteTimestamp = 'datetime';
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = null;


    public function info()
    {
        return $this->hasMany('Info', 'head_id');
    }
}
