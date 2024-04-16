<?php


namespace app\service;

use think\facade\Cache;
use think\facade\Log;
use think\facade\Queue;
use app\traits\EmailTrait;
/**
 * QueueService
 * created on 2021/12/21 14:31
 * created by chengzhigang
 */
class QueueService extends BaseService
{
    use EmailTrait;
    //执行队列
    public static function push($jobHandlerClassName,$jobData,$jobName=""){
        if(empty($jobName)){
            $jobName = config('queue.connections.redis.queue');
        }
        return Queue::push($jobHandlerClassName,$jobData,$jobName);
    }
    //延时任务
    public static function later($delay,$jobHandlerClassName,$jobData,$jobName=""){
        if(empty($jobName)){
            $jobName = config('queue.connections.redis.queue');
        }
        return Queue::later($delay,$jobHandlerClassName,$jobData,$jobName);
    }
    //记录队列数据
    public function record($jobHandlerClassName,$jobData){
        $utimestamp = microtime(true);
        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);
        $key = 'queueJob_' .time() . $milliseconds;
        $data['className'] = $jobHandlerClassName;
        $data['jobTime'] = date('Y-m-d H:i:s');
        $data['data'] = $jobData;
        Cache::store('redis')->set($key,$data);
        $this->send(config('setting.email'),config('setting.web_name').'失败队列通知','失败队列：'.$jobHandlerClassName.',队列ID：'.$key);
    }

    //失败队列回调
    public function failed($payload){
        Log::info($payload);
        $this->record($payload['job'],$payload['data']);
    }

    //查看失败队列
    public function info($jobId){
        $data = Cache::store('redis')->get($jobId);
        if($data){
            return $this->success($data);
        }
        return $this->error('队列不存在');
    }

    //删除失败队列
    public function delete($jobId){
        $res = Cache::store('redis')->delete($jobId);
        if($res){
            return $this->success([],'删除成功');
        }else{
            return $this->error('删除失败');
        }
    }

    //队列重试机制
    public function retry($jobId){
        $jobData = Cache::store('redis')->get($jobId);
        if($jobData){
            $res = self::push($jobData['className'],$jobData['data']);
            if($res!==false){
                return $this->success([],'重试成功');
            }else{
                return $this->error('重试失败：队列有问题');
            }
        }else{
            return $this->error('重试失败：队列不存在');
        }
    }
}
