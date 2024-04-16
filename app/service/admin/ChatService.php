<?php


namespace app\service\admin;

use app\model\Admin;
use app\model\AdminChatData;
use app\model\AdminRelate;
use app\model\ApplyFriend;
use app\service\BaseService;
use app\service\QueueService;
use think\facade\Db;
use think\facade\Log;

/**
 * ChatService
 * created on 2021/12/17 11:26
 * created by chengzhigang
 */
class ChatService extends BaseService
{

    /**
     * @desc 获取用户
     * @method getUser
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/12/17 16:23
     */
    public function getUser(array $param){
       try{
           $data = Admin::where('username','like',$param['account'])->where('status',2)->field(['id','username','nickname','avatar','sign'])->select()->toArray();
           return $this->success(['data'=>$data]);
       }catch (\Exception $exception){
           $this->recordLog($exception);
           return $this->error();
       }
    }

    /**
     * @desc 申请好友
     * @method addFriend
     * @param $uid
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/12/20 15:35
     */
    public function addFriend($uid,array $param){
        try{
            if($uid==$param['touid']){
                return $this->error('不能添加自己为好友');
            }
            $relate = AdminRelate::where('uid',$uid)->where('touid',$param['touid'])->find();
            if(!empty($relate)){
                return $this->error('已是好友');
            }
            $apply = ApplyFriend::where('uid',$uid)->where('touid',$param['touid'])->where('status',1)->find();
            if($apply){
                return $this->error('重复申请');
            }
            $res = ApplyFriend::addApply($uid,$param['touid'],$param['remark']);
            if(!$res){
                throw new \Exception('新增失败');
            }else{
                QueueService::later(config('setting.apply_friend_expire'),'app\job\ApplyFriendExpire',['id'=>$res->id]);
                return $this->success();
            }
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 申请列表
     * @method getApplyList
     * @param $uid
     * @return array
     * @author chengzhigang
     * @time 2021/12/21 14:13
     */
    public function getApplyList($uid){
        try{
            $data = ApplyFriend::alias('a')->join('admin b','a.uid=b.id')->where('a.touid',$uid)
                ->field(['a.id','a.touid','b.username','b.nickname','b.avatar','a.remark','a.status'])->order('a.create_time desc')
                ->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 新增好友
     * @method agreeFriendApply
     * @param $uid
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/12/22 18:02
     */
    public function agreeFriendApply(array $param=[]){
        try{
            Db::startTrans();
            $apply = ApplyFriend::where('id',$param['id'])->where('status',1)->field(['touid','uid'])->lock(true)->find();
            if(empty($apply)){
                return $this->error('申请不存在');
            }
            ApplyFriend::update(['status'=>2],['id'=>$param['id']]);
            if($toapply = ApplyFriend::where('uid',$apply['touid'])->where('touid',$apply['uid'])->where('status',1)->find()){
                ApplyFriend::update(['status'=>2],['id'=>$toapply['id']]);
            }
            AdminRelate::addFriend($apply['uid'],$apply['touid'],1,$param['remark']);
            Db::commit();
            return $this->success([],'添加成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 好友列表
     * @method getFriendList
     * @param $uid
     * @return array
     * @author chengzhigang
     * @time 2021/12/23 11:03
     */
    public function getFriendList($uid){
        try{
            $data = AdminRelate::alias('a')->join('admin b','a.uid=b.id')->where('a.touid',$uid)
                ->field(['a.uid as id','b.username','b.nickname','b.avatar','a.remark','b.sign','a.status'])->order('a.status desc,a.create_time desc')
                ->select()->toArray();
            return $this->success(['data'=>$data]);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 获取历史消息
     * @method getHistoryMsg
     * @param $uid
     * @param $param
     * @return array
     * @author chengzhigang
     * @time 2021/12/24 9:50
     */
    public function getHistoryMsg($uid,$param){
        try{
            $touid = $param['touid'];
            $data = AdminChatData::where(function ($query) use ($uid,$touid){
                $query->where('uid',$uid)->where('touid',$touid);
            })->whereOr(function ($query) use ($uid,$touid){
                $query->where('uid',$touid)->where('touid',$uid);
            })->field(['id','uid','touid','message','type','create_time'])->order('create_time desc')
            ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

}
