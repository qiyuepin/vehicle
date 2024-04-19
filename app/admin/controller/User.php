<?php


namespace app\admin\controller;

use app\Request;
use app\service\admin\UserService;

/**
 * User
 * created on 2021/10/28 17:56
 * created by chengzhigang
 */
class User extends Base
{

    /**
     * @desc 获取用户信息和权限
     * @method info
     * @param Request $request
     * @param UserService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/10/29 17:17
     */
    public function info(Request $request,UserService $service){
        // dump($request -> uid);
        $id = $request -> uid;
        $data = $service -> info($id);
        return $this->responseData($data);
    }

    public function author(UserService $service){
        $data = $service -> author();
        return $this->responseData($data);
    }

    public function updateAuthor(Request $request,UserService $service){
        $param = $request->param();
        $data = $service -> updateAuthor($param);
        return $this->responseData($data);
    }

    /**
     * @desc 退出登录
     * @method logout
     * @param UserService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/2 16:05
     */
    public function logout(Request $request,UserService $service){
        $id = $request -> uid;
        $data = $service -> logout($request->header('Authorization'),$id);
        return $this->responseData($data);
    }

    /**
     * @desc 保存信息
     * @method saveInfo
     * @param Request $request
     * @param UserService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2022/11/11 15:21
     */
    public function saveInfo(Request $request,UserService $service){
        $id = $request -> uid;
        $param = $request->param();
        $data = $service -> saveInfo($id,$param);
        return $this->responseData($data);
    }

    public function updatePass(Request $request,UserService $service){
        $id = $request -> uid;
        $param = $request->param();
        $data = $service -> updatePass($id,$param);
        return $this->responseData($data);
    }
}
