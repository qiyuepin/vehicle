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
    public function getcartrailer(Request $request,InfoService $service){
        $param = $request->param();
        $data = $service -> getcartrailer($param);
        return $this->responseData($data);
       
    }
    public function getcartrailerinfo(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> getcartrailerinfo($param);
        return $this->responseData($data);
       
    }
    public function addcartrailer(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> addcartrailer($param);
        return $this->responseData($data);
    }
    public function editcartrailer(Request $request,InfoService $service){
        $param = $request->param();
     
        $data = $service -> editcartrailer($param);
        return $this->responseData($data);
    }
    public function delcartrailer(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> delcartrailer($param);
        return $this->responseData($data);
    }


    public function getescort(Request $request,InfoService $service){
        $param = $request->param();
        $data = $service -> getescort($param);
        return $this->responseData($data);
       
    }
    public function getescortinfo(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> getescortinfo($param);
        return $this->responseData($data);
       
    }
    public function addescort(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> addescort($param);
        return $this->responseData($data);
    }
    public function editescort(Request $request,InfoService $service){
        $param = $request->param();
     
        $data = $service -> editescort($param);
        return $this->responseData($data);
    }
    public function delescort(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> delescort($param);
        return $this->responseData($data);
    }

    public function getinfolist(Request $request,InfoService $service){
        $param = $request->param();
        $data = $service -> getinfolist($param);
        return $this->responseData($data);
       
    }
    public function getinfo(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> getinfo($param);
        return $this->responseData($data);
       
    }
    public function addinfo(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> addinfo($param);
        return $this->responseData($data);
    }
    public function editinfo(Request $request,InfoService $service){
        $param = $request->param();
     
        $data = $service -> editinfo($param);
        return $this->responseData($data);
    }
    public function delinfo(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> delinfo($param);
        return $this->responseData($data);
    }
    public function getcarlist(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> getcarlist($param);
        return $this->responseData($data);
    }
    public function getfactory(Request $request,InfoService $service){
        // dump($request);
        $param = $request->param();
        $data = $service -> getfactory($param);
        return $this->responseData($data);
       
    }
    public function getfactoryinfo(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> getfactoryinfo($param);
        return $this->responseData($data);
       
    }
    public function addfactory(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> addfactory($param);
        return $this->responseData($data);
    }
    public function editfactory(Request $request,InfoService $service){
        $param = $request->param();
     
        $data = $service -> editfactory($param);
        return $this->responseData($data);
    }
    public function delfactory(Request $request,InfoService $service){
        $param = $request->param();

        $data = $service -> delfactory($param);
        return $this->responseData($data);
    }
}
