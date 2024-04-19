<?php


namespace app\admin\controller;

use app\admin\validate\AdminValidate;
use app\Request;
use app\service\admin\InfoService;

class Info extends Base
{

    public function getcarscope(Request $request,InfoService $service){
        $param = $request->param();
        $data = $service -> getcarscope($param);
        return $this->responseData($data);
       
    }

    public function getcarhead(Request $request,InfoService $service){
        $param = $request->param();
        $data = $service -> getcarhead($param);
        return $this->responseData($data);
       
    }

    public function getcarheadinfo(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> getcarheadinfo($param);
        return $this->responseData($data);
       
    }
    public function addcarhead(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> addcarhead($param);
        return $this->responseData($data);
    }
    public function editcarhead(Request $request,InfoService $service){
        $param = $request->param();
     
        $data = $service -> editcarhead($param);
        return $this->responseData($data);
    }
    public function delcarhead(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> delcarhead($param);
        return $this->responseData($data);
    }
    public function gettrailer(Request $request,InfoService $service){
        $param = $request->param();
        $data = $service -> gettrailer($param);
        return $this->responseData($data);
       
    }
    public function gettrailerinfo(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> gettrailerinfo($param);
        return $this->responseData($data);
       
    }
    public function addtrailer(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> addtrailer($param);
        return $this->responseData($data);
    }
    public function edittrailer(Request $request,InfoService $service){
        $param = $request->param();
     
        $data = $service -> edittrailer($param);
        return $this->responseData($data);
    }
    public function deltrailer(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> deltrailer($param);
        return $this->responseData($data);
    }
}
