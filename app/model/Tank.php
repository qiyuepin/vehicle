<?php


namespace app\model;

use think\facade\Cache;
use think\Model;
use think\model\concern\SoftDelete;

/**
 * Tank
 * created on 2024/12/16 17:24
 * created by hl
 */
class Tank extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $name = 'admin_tank';
    protected $autoWriteTimestamp = 'datetime';


}
