<?php
declare (strict_types = 1);

namespace app\admin\controller;

/**
 * Error
 * created on 2021/7/12 10:52
 * created by chengzhigang
 */
class Error extends Base
{
    public function __call($name, $arguments)
    {
        return $this->fail(404,'不存在的请求地址');
    }
}
