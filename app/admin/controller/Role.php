<?php


namespace app\admin\controller;

use app\admin\validate\RoleValidate;
use app\Request;
use app\service\admin\RoleService;

/**
 * Role
 * created on 2021/11/5 16:28
 * created by chengzhigang
 */
class Role extends Base
{

    /**
     * @desc 获取角色列表
     * @method getList
     * @param Request $request
     * @author chengzhigang
     * @time 2021/11/2 17:55
     */
    public function getList(Request $request,RoleValidate $validate,RoleService $service){
        $param = $request->param();
        if(!$validate->scene('getList')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getList($param);
        return $this->responseData($data);
    }

    /**
     * @desc 获取角色
     * @method getInfo
     * @param Request $request
     * @param RoleValidate $validate
     * @param RoleService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/5 17:33
     */
    public function getInfo(Request $request,RoleValidate $validate,RoleService $service){
        $param = $request->param();
        if(!$validate->scene('getInfo')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getInfo($param);
        return $this->responseData($data);
    }

    /**
     * @desc 新增角色
     * @method addRole
     * @param Request $request
     * @param RoleValidate $validate
     * @param RoleService $service
     * @author chengzhigang
     * @time 2021/11/8 8:58
     */
    public function addRole(Request $request,RoleValidate $validate,RoleService $service){
        $param = $request->param();
        if(!$validate->scene('addRole')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> addRole($param);
        return $this->responseData($data);
    }

    /**
     * @desc 编辑角色
     * @method editRole
     * @param Request $request
     * @param RoleValidate $validate
     * @param RoleService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/8 9:05
     */
    public function editRole(Request $request,RoleValidate $validate,RoleService $service){
        $param = $request->param();
        if(!$validate->scene('editRole')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> editRole($param);
        return $this->responseData($data);
    }

    /**
     * @desc 更改角色状态
     * @method changeStatus
     * @param Request $request
     * @param RoleValidate $validate
     * @param RoleService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/8 9:24
     */
    public function changeStatus(Request $request,RoleValidate $validate,RoleService $service){
        $param = $request->param();
        if(!$validate->scene('changeStatus')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> changeStatus($param);
        return $this->responseData($data);
    }

    /**
     * @desc 删除角色
     * @method deleteRole
     * @param Request $request
     * @param RoleValidate $validate
     * @param RoleService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/8 10:26
     */
    public function deleteRole(Request $request,RoleValidate $validate,RoleService $service){
        $param = $request->param();
        if(!$validate->scene('deleteRole')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> deleteRole($param);
        return $this->responseData($data);
    }
}
