<?php


namespace app\job;

use app\model\ApplyFriend;
use app\service\QueueService;
use think\queue\Job;

/**
 * ApplyFriendExpire
 * created on 2021/12/21 14:30
 * created by chengzhigang
 */
class ApplyFriendExpire
{

    public $payload;

    public function fire(Job $job, $data){
        //执行成功
        $this->payload = $job->payload();
        ApplyFriend::updateStatus($data['id'],4);
        $job->delete();
    }

    public function failed(){
        (new QueueService)->failed($this->payload);
    }
}
