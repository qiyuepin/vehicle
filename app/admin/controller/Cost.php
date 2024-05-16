<?php


namespace app\admin\controller;

use app\admin\validate\CostValidate;
use app\Request;
use app\service\admin\CostService;

/**
 * Cost
 * created on 2021/11/2 17:54
 * created by chengzhigang
 */
class Cost extends Base
{

    /**
     * @desc 获取广告列表
     * @method getList
     * @param Request $request
     * @author chengzhigang
     * @time 2021/11/2 17:55
     */
    public function getcost(Request $request,CostService $service){
        $param = $request->param();
    
        $data = $service -> getcost($param);
        return $this->responseData($data);
    }

    /**
     * @desc 更改广告状态
     * @method change
     * @param Request $request
     * @param CostValidate $validate
     * @param CostService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/3 10:50
     */
    public function change(Request $request,CostService $service){
        $param = $request->param();
   
        $data = $service -> changeStatus($param);
        return $this->responseData($data);
    }

    /**
     * @desc 新增广告
     * @method add
     * @param Request $request
     * @param CostValidate $validate
     * @param CostService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/4 17:02
     */
    public function addcost(Request $request,CostService $service){
        $param = $request->param();
        $authorization = $request->header('Authorization');
        // dump($authorization);die;
        $data = $service -> addcost($param,$authorization);
        return $this->responseData($data);
    }

    /**
     * @desc 获取广告详情
     * @method info
     * @param Request $request
     * @param CostValidate $validate
     * @param CostService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/4 18:06
     */
    public function getinfo(Request $request,CostService $service){
        $param = $request->param();
        
        $data = $service -> getinfo($param);
        return $this->responseData($data);
    }

    /**
     * @desc 方法描述
     * @method edit
     * @param Request $request
     * @param CostValidate $validate
     * @param CostService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/5 9:32
     */
    public function editcost(Request $request,CostService $service){
        $param = $request->param();
   
        $data = $service -> editcost($param);
        return $this->responseData($data);
    }

    /**
     * @desc 删除广告
     * @method delete
     * @param Request $request
     * @param CostValidate $validate
     * @param CostService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/5 11:05
     */
    public function delcost(Request $request,CostService $service){
        $param = $request->param();

        $data = $service -> delcost($param);
        return $this->responseData($data);
    }

    public function getcosttype(Request $request,CostService $service){
        $param = $request->param();
    
        $data = $service -> getcosttype($param);
        return $this->responseData($data);
    }

    public function getperiod(Request $request,CostService $service){
        $param = $request->param();
    
        $data = $service -> getperiod($param);
        return $this->responseData($data);
    }

    public function getcostlist(Request $request,CostService $service){
        $param = $request->param();
    
        $data = $service -> getcostlist($param);
        return $this->responseData($data);
    }
}
