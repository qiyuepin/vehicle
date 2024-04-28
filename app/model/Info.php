<?php


namespace app\model;

use think\facade\Cache;
use think\Model;


class Info extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'admin_careinfo';
    protected $autoWriteTimestamp = 'datetime';

    public function Logs()
    {
        return $this->hasMany('InfoLog', 'info_id');
    }

    public function heads()
    {
        return $this->belongsTo('Carhead', 'id');
    }

    public static function carhead($head_id)
    {
        // dump($head_id);
        $carhead_plate = self::alias('i')->join('admin_carhead c','c.id=i.head_id')->where('i.head_id',$head_id)->value('carhead_plate');
        return $carhead_plate;
    }

    public static function cartrailer($trailer_id)
    {
        $trailer_plate = self::alias('i')->join('admin_cartrailer c','c.id=i.trailer_id')->where('i.trailer_id',$trailer_id)->value('trailer_plate');
        return $trailer_plate;
    }

    public static function cardriver($driver_id)
    {
        $driver = self::alias('i')->join('admin c','c.id=i.driver_id')->where('i.driver_id',$driver_id)->value('username');
        return $driver;
    }

    public static function carescort($escort_id)
    {
        $escort = self::alias('i')->join('admin_carescort c','c.id=i.escort_id')->where('i.escort_id',$escort_id)->value('name');
        return $escort;
    }
}
