<?php


namespace app\admin\controller;

use app\admin\validate\AdminValidate;
use app\Request;
use app\service\admin\InfoService;
use app\service\admin\PlanService;
use app\service\admin\UserService;

class Plan extends Base
{

    public function getplaninfo(Request $request,PlanService $service){
        $param = $request->param();
        $data = $service -> getplaninfo($param);
        return $this->responseData($data);
       
    }

    public function getnormal(Request $request,PlanService $service){
        $param = $request->param();
        $data = $service -> getnormal($param);
        return $this->responseData($data);
       
    }

    public function getnormalinfo(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> getnormalinfo($param);
        return $this->responseData($data);
       
    }
    public function addnormal(Request $request,PlanService $service){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        $data = $service -> addnormal($param,$authorization);
        return $this->responseData($data);
    }
    public function editnormal(Request $request,PlanService $service){
        $param = $request->param();
     
        $data = $service -> editnormal($param);
        return $this->responseData($data);
    }
    public function delnormal(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> delnormal($param);
        return $this->responseData($data);
    }
    public function gettemporary(Request $request,PlanService $service){
        $param = $request->param();
        $data = $service -> gettemporary($param);
        return $this->responseData($data);
       
    }
    public function gettemporaryinfo(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> gettemporaryinfo($param);
        return $this->responseData($data);
       
    }
    public function addtemporary(Request $request,PlanService $service){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        $data = $service -> addtemporary($param,$authorization);
        return $this->responseData($data);
    }
    public function edittemporary(Request $request,PlanService $service){
        $param = $request->param();
     
        $data = $service -> edittemporary($param);
        return $this->responseData($data);
    }
    public function deltemporary(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> deltemporary($param);
        return $this->responseData($data);
    }
    public function getplans(Request $request,PlanService $service){
        $param = $request->param();
        $data = $service -> getplans($param);
        return $this->responseData($data);
       
    }
    public function getplansinfo(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> getplansinfo($param);
        return $this->responseData($data);
       
    }
    public function addplan(Request $request,PlanService $service){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        $data = $service -> addplan($param,$authorization);
        return $this->responseData($data);
    }
    //修改 No.10
    public function addhisplan(Request $request,PlanService $service){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        $data = $service -> addhisplan($param,$authorization);
        return $this->responseData($data);
    }
    public function getPlanCount(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> getPlanCount($param);
        return $this->responseData($data);
    }
    //修改 No.10
    public function editplan(Request $request,PlanService $service){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        $data = $service -> editplan($param,$authorization);
        return $this->responseData($data);
    }
    public function distplan(Request $request,PlanService $service){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        $data = $service -> distplan($param,$authorization);
        return $this->responseData($data);
    }
    public function delplan(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> delplan($param);
        return $this->responseData($data);
    }
    public function driver_normal(Request $request,PlanService $service,UserService $userservice){
        $param = $request->param();
        // dump($param);
        $id = $request -> uid;
        $infoid = $userservice -> info($id);
        // dump($infoid['data']);
        $data = $service -> driver_normal($param,$infoid['data']['username']);
        
        return $this->responseData($data);
    }

    public function driver_sumitnormal(Request $request,PlanService $service,UserService $userservice){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        $data = $service -> driver_sumitnormal($param,$authorization);
        return $this->responseData($data);
    }

    public function homechart(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> homechart($param);
        return $this->responseData($data);
       
    }

    public function notice(Request $request,PlanService $service){
        $param = $request->param();

        $data = $service -> notice($param);
        return $this->responseData($data);
       
    }
}
