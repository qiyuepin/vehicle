<?php


namespace app\model;

use think\facade\Cache;
use think\Model;


class InfoLog extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin_careinfo_log';
    protected $autoWriteTimestamp = 'datetime';

    public function info()
    {
        return $this->belongsTo('Info', 'id');
    }

}
