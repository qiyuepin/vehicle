<?php
declare (strict_types = 1);

namespace app\admin\controller;


use app\service\QueueService;
use app\service\RedisService;

class Index extends Base
{

    public function ceshi(){
        $pool = RedisService::getInstance();
        $redis = $pool->get();
        $a = $redis->get('fd:13');
        var_dump($a);
        return json(['code'=>200,'msg'=>'成功']);
    }
}
