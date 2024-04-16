<?php


namespace app\admin\controller;

use app\admin\validate\AdminValidate;
use app\Request;
use app\service\admin\AdminService;

class Driver extends Base
{


    public function getregulation(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        // dump($param);die;
        if(!$validate->scene('getregulation')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getregulation($param);
        return $this->responseData($data);
       
    }
    public function addregulation(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('addregulation')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> addregulation($param);
        return $this->responseData($data);
    }
    public function getaccident(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        // dump($param);die;
        if(!$validate->scene('getaccident')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getaccident($param);
        return $this->responseData($data);
    }

    public function test(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        // dump($param);die;
        if(!$validate->scene('getregulation')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getregulation($param);
        return $this->responseData($data);
    }
}
