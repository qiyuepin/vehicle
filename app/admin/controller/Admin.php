<?php


namespace app\admin\controller;

use app\admin\validate\AdminValidate;
use app\Request;
use app\service\admin\AdminService;

/**
 * Admin
 * created on 2021/11/2 17:54
 * created by chengzhigang
 */
class Admin extends Base
{

    /**
     * @desc 获取管理员列表
     * @method getList
     * @param Request $request
     * @author chengzhigang
     * @time 2021/11/2 17:55
     */
    public function getList(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('getList')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getList($param);
        return $this->responseData($data);
    }

    /**
     * @desc 更改管理员状态
     * @method changeStatus
     * @param Request $request
     * @param AdminValidate $validate
     * @param AdminService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/3 10:50
     */
    public function changeStatus(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('changeStatus')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> changeStatus($param);
        return $this->responseData($data);
    }

    /**
     * @desc 获取管理员
     * @method getRole
     * @param AdminService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/3 16:35
     */
    public function getRole(AdminService $service){
        $data = $service -> getRole();
        return $this->responseData($data);
    }

    /**
     * @desc 新增管理员
     * @method addAdmin
     * @param Request $request
     * @param AdminValidate $validate
     * @param AdminService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/4 17:02
     */
    public function addAdmin(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('addAdmin')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> addAdmin($param);
        return $this->responseData($data);
    }
    public function adddriverAdmin(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        
        if(!$validate->scene('adddriverAdmin')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        // dump($param);
        $data = $service -> adddriverAdmin($param);
        return $this->responseData($data);
    }
    /**
     * @desc 获取管理员详情
     * @method getInfo
     * @param Request $request
     * @param AdminValidate $validate
     * @param AdminService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/4 18:06
     */
    public function getInfo(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('getInfo')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getInfo($param);
        return $this->responseData($data);
    }

    
    /**
     * @desc 方法描述
     * @method editAdmin
     * @param Request $request
     * @param AdminValidate $validate
     * @param AdminService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/5 9:32
     */
    public function editAdmin(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('editAdmin')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> editAdmin($param);
        return $this->responseData($data);
    }

    /**
     * @desc 删除管理员
     * @method deleteAdmin
     * @param Request $request
     * @param AdminValidate $validate
     * @param AdminService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/5 11:05
     */
    public function deleteAdmin(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('deleteAdmin')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> deleteAdmin($param);
        return $this->responseData($data);
    }

    /**
     * @desc 登录日志
     * @method getLoginLog
     * @param Request $request
     * @param AdminValidate $validate
     * @param AdminService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/9 16:16
     */
    public function getLoginLog(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('getLoginLog')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getLoginLog($param);
        return $this->responseData($data);
    }

    /**
     * @desc 操作日志
     * @method getHandleLog
     * @param Request $request
     * @param AdminValidate $validate
     * @param AdminService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/11/9 16:16
     */
    public function getHandleLog(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('getHandleLog')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getHandleLog($param);
        return $this->responseData($data);
    }

    /**
     * @desc 获取驾驶员管理员列表
     * @method getdriverList
     * @param Request $request
     * @author chengzhigang
     * @time 2021/11/2 17:55
     */
    public function getdriverList(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        // dump($param);
        if(!$validate->scene('getList')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getdriverList($param);
        return $this->responseData($data);
    }

    public function getdriverInfo(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('getdriverInfo')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        // dump($param);
        $data = $service -> getdriverInfo($param);
        return $this->responseData($data);
    }
    public function editdriverAdmin(Request $request,AdminValidate $validate,AdminService $service){
        $param = $request->param();
        if(!$validate->scene('editdriverAdmin')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> editdriverAdmin($param);
        return $this->responseData($data);
    }

}
