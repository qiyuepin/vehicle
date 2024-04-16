<?php


namespace app\admin\controller;

use app\admin\validate\AdvertValidate;
use app\Request;
use app\service\admin\AdvertService;

/**
 * Advert
 * created on 2021/11/2 17:54
 * created by chengzhigang
 */
class Advert extends Base
{

    /**
     * @desc 获取广告列表
     * @method getList
     * @param Request $request
     * @author chengzhigang
     * @time 2021/11/2 17:55
     */
    public function index(Request $request,AdvertValidate $validate,AdvertService $service){
        $param = $request->param();
        if(!$validate->scene('list')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getList($param);
        return $this->responseData($data);
    }

    /**
     * @desc 更改广告状态
     * @method change
     * @param Request $request
     * @param AdvertValidate $validate
     * @param AdvertService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/3 10:50
     */
    public function change(Request $request,AdvertValidate $validate,AdvertService $service){
        $param = $request->param();
        if(!$validate->scene('change')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> changeStatus($param);
        return $this->responseData($data);
    }

    /**
     * @desc 新增广告
     * @method add
     * @param Request $request
     * @param AdvertValidate $validate
     * @param AdvertService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/4 17:02
     */
    public function add(Request $request,AdvertValidate $validate,AdvertService $service){
        $param = $request->param();
        if(!$validate->scene('add')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> addAdvert($param);
        return $this->responseData($data);
    }

    /**
     * @desc 获取广告详情
     * @method info
     * @param Request $request
     * @param AdvertValidate $validate
     * @param AdvertService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/4 18:06
     */
    public function info(Request $request,AdvertValidate $validate,AdvertService $service){
        $param = $request->param();
        if(!$validate->scene('info')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getInfo($param);
        return $this->responseData($data);
    }

    /**
     * @desc 方法描述
     * @method edit
     * @param Request $request
     * @param AdvertValidate $validate
     * @param AdvertService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/5 9:32
     */
    public function edit(Request $request,AdvertValidate $validate,AdvertService $service){
        $param = $request->param();
        if(!$validate->scene('edit')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> editAdvert($param);
        return $this->responseData($data);
    }

    /**
     * @desc 删除广告
     * @method delete
     * @param Request $request
     * @param AdvertValidate $validate
     * @param AdvertService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/5 11:05
     */
    public function delete(Request $request,AdvertValidate $validate,AdvertService $service){
        $param = $request->param();
        if(!$validate->scene('delete')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> deleteAdvert($param);
        return $this->responseData($data);
    }

}
