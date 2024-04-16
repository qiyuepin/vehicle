<?php


namespace app\service\admin;

use app\model\AuthGroup;
use app\model\AuthGroupAccess;
use app\service\BaseService;
use think\facade\Db;

/**
 * RoleService
 * created on 2021/11/2 17:55
 * created by chengzhigang
 */
class RoleService extends BaseService
{

    /**
     * @desc 获取管理员列表
     * @method getList
     * @param array $param
     * @author chengzhigang
     * @time 2021/11/2 17:56
     */
    public function getList($param=[]){
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['title','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])&&$param['status']){
                $where[] = ['status','=',$param['status']];
            }
            $data = AuthGroup::where($where)->field(['id','title','status','create_time'])
                ->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }



    /**
     * @desc 获取角色
     * @method getInfo
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 18:09
     */
    public function getInfo($param=[]){
        try{
            $info = AuthGroup::where('id',$param['id'])->field(['id','title','status','rules'])->find();
            if(empty($info)){
                return $this->error('管理员不存在');
            }
            $info['rules'] = array_map('intval', explode(',',$info['rules']));
            return $this->success($info->toArray());
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 新增角色
     * @method addRole
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/8 9:03
     */
    public function addRole($param=[]){
        try{
            if(isset($param['id'])){
                unset($param['id']);
            }
            $param['rules'] = implode(',',$param['rules']);
            $res = AuthGroup::create($param);
            if($res){
                return $this->success([],'新增成功');
            }else{
                return $this->error('新增失败');
            }
        }catch (\Exception $exception){
            $this -> recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 编辑角色
     * @method editRole
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/8 9:04
     */
    public function editRole($param=[]){
        try{
            $param['rules'] = implode(',',$param['rules']);
            $res = AuthGroup::update($param,['id'=>$param['id']]);
            if($res){
                return $this->success([],'编辑成功');
            }else{
                return $this->error('编辑失败');
            }
        }catch (\Exception $exception){
            $this -> recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 更改状态
     * @method changeStatus
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/8 9:26
     */
    public function changeStatus($param=[]){
        try{
            $res = AuthGroup::update(['status'=>$param['status']],['id'=>$param['id']]);
            if($res){
                return $this->success([],'更改成功');
            }else{
                return $this->error('更改失败');
            }
        }catch (\Exception $exception){
            $this -> recordLog($exception);
            return $this->error();
        }
    }

    /**
     * @desc 删除角色
     * @method deleteRole
     * @param array $param
     * @return array
     * @author chengzhigang
     * @time 2021/11/8 10:30
     */
    public function deleteRole($param=[]){
        try{
            Db::startTrans();
            //删除角色
            $res = AuthGroup::whereIn('id',$param['ids'])->delete();
            if(!$res){
                throw new \Exception('删除角色表失败');
            }
            //删除关联
            AuthGroupAccess::whereIn('group_id',$param['ids'])->delete();
            Db::commit();
            return $this->success([],'删除成功');
        }catch (\Exception $exception){
            Db::rollback();
            $this -> recordLog($exception);
            return $this->error();
        }
    }
}
