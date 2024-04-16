<?php


namespace app\service;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;
use Redis;
/**
 * RedisService
 * created on 2021/12/16 11:06
 * created by chengzhigang
 */
class RedisService
{

    private static $connectionPool = []; //定义一个对象池
    private static $retryNum = 3;//定义重连次数避免死循环
    private static $handleNum = 0;

    public static function getInstance($alias = 'default'){
        if (extension_loaded('redis')) {
            $connections = config('redis.connections');
            if(!isset($connections[$alias])||empty($connections[$alias])){
                throw new \Exception('redis配置文件不存在');
            }
            $pool = new RedisPool((new RedisConfig)
                ->withHost($connections[$alias]['host'])
                ->withPort($connections[$alias]['port'])
                ->withAuth($connections[$alias]['password'])
                ->withDbIndex($connections[$alias]['database'])
            );
            return $pool->get();
        }else{
            throw new \Exception('缺少redis扩展');
        }
    }

    public static function getRedis($alias='default'){
        if (extension_loaded('redis')) {
            if(!array_key_exists($alias,self::$connectionPool)){  //判断连接池中是否存在
                $redis = new \Redis();
                try{
                    $connect = config('redis.connections')[$alias];
                    $config = config('redis.options');
                    $connect['prefix'] = $connect['prefix'] ?? $config['prefix'];
//                    $connect['timeout'] = $connect['timeout'] ?? $config['timeout'];
//                    if($connect['connect']=='pconnect'){
//                        $redis->pconnect($connect['host'], $connect['port']);
//                    }else{
                        $redis->connect($connect['host'], $connect['port']);
//                    }
                    if(!empty($connect['password'])){
                        $redis->auth($connect['password']);
                    }
                    $redis->select($connect['database']);
                    if (!empty($connect['prefix'])) {
                        $redis->setOption(Redis::OPT_PREFIX,$connect['prefix']);
                    }
                    self::$connectionPool[$alias] = $redis;
                }catch (\Exception $exception){
                    self::$handleNum += 1;
                    if(self::$handleNum>=self::$retryNum){
                        throw new \Exception('redis链接失败');
                    }else{
                        $redis->close();
                        unset($redis);
                        return self::getRedis($alias);
                    }
                }
            }
            return self::$connectionPool[$alias];
        }else{
            throw new \Exception('缺少redis扩展');
        }
    }
}
