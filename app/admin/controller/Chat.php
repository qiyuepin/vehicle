<?php


namespace app\admin\controller;

use app\admin\validate\ChatValidate;
use app\Request;
use app\service\admin\ChatService;
use app\service\admin\SwooleService;
use think\facade\Log;

/**
 * Chat
 * created on 2021/12/17 11:23
 * created by chengzhigang
 */
class Chat extends Base
{

    /**
     * @desc 搜索用户
     * @method getUser
     * @param Request $request
     * @param ChatValidate $validate
     * @param ChatService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/12/17 16:09
     */
    public function getUser(Request $request,ChatValidate $validate,ChatService $service){
        $param = $request->param();
        if(!$validate->scene('getUser')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getUser($param);
        return $this->responseData($data);
    }

    /**
     * @desc 申请好友
     * @method addFriend
     * @param Request $request
     * @param ChatValidate $validate
     * @param ChatService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/12/17 11:28
     */
    public function addFriend(Request $request,ChatValidate $validate,ChatService $service){
        $param = $request->param();
        if(!$validate->scene('addFriend')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> addFriend($request->uid,$param);
        return $this->responseData($data);
    }

    /**
     * @desc 申请列表
     * @method getApplyList
     * @param Request $request
     * @param ChatService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/12/21 14:03
     */
    public function getApplyList(Request $request,ChatService $service){
        $data = $service -> getApplyList($request->uid);
        return $this->responseData($data);
    }

    /**
     * @desc 同意好友申请
     * @method agreeFriendApply
     * @param Request $request
     * @param ChatValidate $validate
     * @param ChatService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/12/22 17:41
     */
    public function agreeFriendApply(Request $request,ChatValidate $validate,ChatService $service){
        $param = $request->param();
        if(!$validate->scene('agreeFriendApply')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> agreeFriendApply($param);
        return $this->responseData($data);
    }

    /**
     * @desc 获取好友列表
     * @method getFriendList
     * @param Request $request
     * @param ChatService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/12/23 11:00
     */
    public function getFriendList(Request $request,ChatService $service){
        $data = $service -> getFriendList($request->uid);
        return $this->responseData($data);
    }

    /**
     * @desc 获取历史消息
     * @method getHistoryMsg
     * @param Request $request
     * @param ChatValidate $validate
     * @param ChatService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/12/28 17:08
     */
    public function getHistoryMsg(Request $request,ChatValidate $validate,ChatService $service){
        $param = $request->param();
        if(!$validate->scene('getHistoryMsg')->check($param)){
            return $this->fail(400,$validate->getError());
        }
        $data = $service -> getHistoryMsg($request->uid,$param);
        return $this->responseData($data);
    }

    /**
     * @desc 获取会话列表
     * @method getSessionList
     * @param Request $request
     * @param ChatService $service
     * @return \think\response\Json
     * @author chengzhigang
     * @time 2021/12/28 17:09
     */
    public function getSessionList(Request $request,SwooleService $service){
        $data = $service -> getSessionList($request->uid);
        return $this->responseData(['code'=>200,'msg'=>'请求成功','data'=>$data]);
    }
}
