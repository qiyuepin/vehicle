<?php
declare (strict_types = 1);

namespace app\index\controller;

use app\BaseController;

class Index extends BaseController
{
    public function index()
    {
        return 'Hello World';
    }

    public function hello(){
        echo 'hello';
    }
}
