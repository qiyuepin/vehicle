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
    public function getregulationinfo(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();

        if(!$validate->scene('getregulationinfo')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getregulationinfo($param);
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
    public function editregulation(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('editregulation')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> editregulation($param);
        return $this->responseData($data);
    }
    public function delregulation(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('delregulation')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> delregulation($param);
        return $this->responseData($data);
    }
    
    public function addaccident(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('addaccident')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> addaccident($param);
        return $this->responseData($data);
    }
    public function editaccident(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('editaccident')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> editaccident($param);
        return $this->responseData($data);
    }
    public function getaccident(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
         //dump($param);die;
        if(!$validate->scene('getaccident')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getaccident($param);
        return $this->responseData($data);
    }
    public function getaccidentinfo(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();

        if(!$validate->scene('getaccidentinfo')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getaccidentinfo($param);
        return $this->responseData($data);

    }
    public function delaccident(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('delaccident')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> delaccident($param);
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
