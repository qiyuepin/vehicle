<?php
declare (strict_types = 1);

namespace app\admin\controller;

use app\admin\validate\AuthValidate;
use app\Request;
use app\service\admin\AuthService;

/**
 * AuthValidate
 * created on 2021/7/12 10:02
 * created by chengzhigang
 */
class Auth extends Base
{

    /**
     * @desc 登录
     * @method login
     * @param Request $request
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author chengzhigang
     * @time 2021/7/12 12:29
     */
    public function login(Request $request,AuthValidate $validate,AuthService $service){
        $param = $request->param();
        // dump($param);
        if(!$validate->scene('login')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> login($param);
        return $this->responseData($data);
    }

    public function freshToken(Request $request,AuthService $service){
        $param = $request->param();
        $data = $service -> freshToken($param);
        return $this->responseData($data);
    }

    /**
     * @desc 获取权限树
     * @method getRules
     * @param AuthService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/5 17:23
     */
    public function getRules(AuthService $service){
        $data = $service -> getRules();
        return $this->responseData($data);
    }

    /**
     * @desc 获取权限列表
     * @method getList
     * @param Request $request
     * @param AuthValidate $validate
     * @param AuthService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/8 13:16
     */
    public function getList(AuthService $service){
        $data = $service -> getList();
        return $this->responseData($data);
    }

    /**
     * @desc 获取二级权限树
     * @method getSecondRules
     * @param AuthService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/9 9:28
     */
    public function getSecondRules(AuthService $service){
        $data = $service -> getSecondRules();
        return $this->responseData($data);
    }

    /**
     * @desc 新增权限
     * @method addRule
     * @param Request $request
     * @param AuthValidate $validate
     * @param AuthService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/9 10:12
     */
    public function addRule(Request $request,AuthValidate $validate,AuthService $service){
        $param = $request->param();
        if(!$validate->scene('addRule')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> addRule($param);
        return $this->responseData($data);
    }

    /**
     * @desc 获取权限
     * @method getInfo
     * @param Request $request
     * @param AuthValidate $validate
     * @param AuthService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/9 10:58
     */
    public function getInfo(Request $request,AuthValidate $validate,AuthService $service){
        $param = $request->param();
        if(!$validate->scene('getInfo')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getInfo($param);
        return $this->responseData($data);
    }

    /**
     * @desc 编辑权限
     * @method editRule
     * @param Request $request
     * @param AuthValidate $validate
     * @param AuthService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/9 11:05
     */
    public function editRule(Request $request,AuthValidate $validate,AuthService $service){
        $param = $request->param();
        if(!$validate->scene('editRule')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> editRule($param);
        return $this->responseData($data);
    }

    /**
     * @desc 删除权限
     * @method deleteRule
     * @param Request $request
     * @param AuthValidate $validate
     * @param AuthService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/9 13:18
     */
    public function deleteRule(Request $request,AuthValidate $validate,AuthService $service){
        $param = $request->param();
        if(!$validate->scene('deleteRule')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> deleteRule($param);
        return $this->responseData($data);
    }

}
