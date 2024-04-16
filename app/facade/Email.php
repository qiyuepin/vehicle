<?php


namespace app\facade;

use think\Facade;

/**
 * Email
 * created on 2021/8/13 16:16
 * created by chengzhigang
 */
class Email extends Facade
{

    protected static function getFacadeClass()
    {
        return 'app\traits\EmailTrait';
    }
}
