<?php


namespace app\admin\controller;

use app\admin\validate\AdminValidate;
use app\Request;
use app\service\admin\TankService;

class Tank extends Base
{

    public function getlist(Request $request,TankService $service){
        // dump($request);
        $param = $request->param();
        $data = $service -> getlist($param);
        return $this->responseData($data);
       
    }
    public function getinfo(Request $request,TankService $service){
        $param = $request->param();

        $data = $service -> getinfo($param);
        return $this->responseData($data);
       
    }
    public function add(Request $request,TankService $service){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        $data = $service -> add($param,$authorization);
        // $data = $service -> add($param);
        return $this->responseData($data);
    }
    public function edit(Request $request,TankService $service){
        $param = $request->param();

        $data = $service -> edit($param);

        return $this->responseData($data);
    }
    // public function edit(Request $request,TankService $service){
    //     $param = $request->param();
     
    //     $data = $service -> edit($param);
    //     return $this->responseData($data);
    // }
    public function del(Request $request,TankService $service){
        $param = $request->param();

        $data = $service -> del($param);
        return $this->responseData($data);
    }

}
