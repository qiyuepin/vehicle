<?php
declare (strict_types=1);

namespace app\listener\admin;

use app\model\AdminLoginLog;

/**
 * UserLogin
 * created on 2021/11/9 14:37
 * created by chengzhigang
 */
class UserLogin
{
    /**
     * @desc 登录事件
     * @method handle
     * @param $event
     * @author chengzhigang
     * @time 2021/11/9 14:39
     */
    public function handle($event)
    {
        list($user, $loginData) = $event;
        AdminLoginLog::addLoginLog($user['id'],$loginData['login_time'],$loginData['login_ip']);
    }
}
