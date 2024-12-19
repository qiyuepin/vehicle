<?php


namespace app\service\admin;

use app\model\Admin;
use app\model\AdminLoginLog;
use app\model\Tank;
use app\model\Plan;
use app\model\AuthGroup;
use app\model\AuthGroupAccess;
use app\service\BaseService;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Log;
use app\traits\TokenTrait;
use Overtrue\Pinyin\Pinyin;

/**
 * TankService
 * created on 2021/11/2 17:55
 * created by chengzhigang
 */
class TankService extends BaseService
{
    use TokenTrait;

    public function getTank($param=[]){
        try{
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['driver_name|head_num','like','%'.$param['keywords'].'%'];
            }
            if(isset($param['status'])&&$param['status'] !=''){
                $where[] = ['status','=',$param['status']];
            }

            if(isset($param['platform'])&&$param['platform'] == "pc"){//电脑端

                $data = Tank::where($where)->order(['create_time'=>'desc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();

            }
            else if(isset($param['platform'])&&$param['platform'] == "app"){//app端
                $data = Db::name("admin_carplan_period")->where($where)->order(['create_time'=>'desc'])
                ->select()->toArray();
                foreach($data as $key => $value ){
                    $data[$key]['total'] = Tank::where('period_id_driver',$value['period_id_driver'])->sum('Tank_money');
                }
            }
            else if(isset($param['platform'])&&$param['platform'] == "selectdriver"){//app端
                $data = Db::name("admin_carplan")->where($where)->where('driver_status',1)->find();
                
                if($data){
                    return $this->success(['data'=>$data]);
                }else{
                    // dump(88);die;
                    $data = Db::name("admin_carplan_period")->where($where)->where('status',1)->find();
                    // dump(88);die;
                    return $this->success(['data'=>$data]);
                }
            }
            else if(isset($param['platform'])&&$param['platform'] == "excelall"){//导出excel
                $group = Db::name("admin_carplan_period")->where($where)->group('period_id_driver')->column('period_id_driver');
                // dump($group);die;
                // $where[] = ['status','=',$param['status']];
                $data = Db::name("admin_Tank")->where('period_id_driver','in',$group)->select()->toArray();
    
                $newData = []; // 新数组用于存放处理后的数据

                foreach ($data as $key => $value) {
                    // 获取当前 period_id_driver 对应的 start_mile 和 end_mile
                    $start_mile = Db::name("admin_carplan_period")->where('period_id_driver', $value['period_id_driver'])->value('start_mile');
                    $end_mile = Db::name("admin_carplan_period")->where('period_id_driver', $value['period_id_driver'])->value('end_mile');

                    // 判断是否需要新增数组元素
                    if ($key === 0 || $value['period_id_driver'] !== $data[$key - 1]['period_id_driver']) {
                        // 如果是第一个元素或者当前 period_id_driver 不同于前一个元素的 period_id_driver，则新增数组元素
                        // $newValue = $value; // 复制原始数据到新数组元素
                        $newValue['period_id_driver'] = $value['period_id_driver'];
                        $newValue['start_mile'] = $start_mile; // 添加 start_mile
                        $newValue['end_mile'] = $end_mile; // 添加 end_mile
                        $newData[] = $newValue; // 将新元素添加到新数组中
                        $newData[] = $value;
                    } else {
                        // 如果当前 period_id_driver 与前一个元素的 period_id_driver 相同，直接将原始数据添加到新数组中
                        $newData[] = $value;
                    }
                }

                // 如果需要将处理后的数据重新赋值给原始数组 $data，可以使用以下语句
                $data = $newData;

                



            }

            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    
    public function getlist($param=[]){
        try{
            // dump($param);
            $where = [];
            if(isset($param['keywords'])&&$param['keywords']){
                $where[] = ['name|city','like','%'.$param['keywords'].'%'];
            }
            
            if(isset($param['platform'])&&$param['platform'] == "pc"){
                // $data = Db::name("admin_Tank")->where($where)->order(['create_time'=>'desc'])
                // ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();
                $data = Tank::where($where)->order(['pname_letter'=>'asc','city_letter'=>'asc','name_letter'=>'asc'])
                ->paginate(['page' => $param['page'], 'list_rows' => $param['limit']])->toArray();

            }
            elseif(isset($param['platform'])&&$param['platform'] == "app"){
                $data = Tank::where($where)->order(['pname_letter'=>'asc','city_letter'=>'asc','name_letter'=>'asc'])
                ->select()->toArray();
                // dump($data);die;
                // return $this->success($data->toArray());
            }
            
            
            return $this->success($data);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function add($param=[],$Authorization){
        try{
            if(isset($param['id'])){
                unset($param['id']);
            }
            $pinyin = new Pinyin();
            $param['pname_letter'] =  $pinyin->abbr($param['pname']);
            $param['city_letter'] =  $pinyin->abbr($param['city']);
            $param['name_letter'] =  $pinyin->abbr($param['name']);
            // dump($param);die;
            // $token = $this->getValue('664461343744a');
            $userid = $this->getValue($Authorization);

            $param['tank_creater'] = Admin::where('id',$userid)->value('username');
  
            $res = Tank::create($param);
            if(!$res){
                throw new \Exception('新增失败');
            }
            return $this->success([],'新增成功');
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    public function getinfo($param=[]){
        try{
            
            if(isset($param['id'])){//费用详情
                // dump('--id--');
                $info = Tank::where('id',$param['id'])->find();
                if(empty($info)){
                    return $this->error('不存在');
                }else{
                    $info = $info->toArray();
                   
                }
            }
            else if(isset($param['period_id'])){//周期详情
                // dump($param);die;
                
                $info = Db::name('admin_carplan_period')->where('id',$param['period_id'])->find();
                
            }
            // dump($info);die;
            if(empty($info)){
                return $this->error('不存在');
            }
            return $this->success($info);
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }


    public function edit($param=[]){
        try{
            $admin = Tank::where('id',$param['id'])->find();
            if(empty($admin)){
                throw new \Exception('不存在');
            }
            $pinyin = new Pinyin();
            $param['pname_letter'] =  $pinyin->abbr($param['pname']);
            $param['city_letter'] =  $pinyin->abbr($param['city']);
            $param['name_letter'] =  $pinyin->abbr($param['name']);
            $res = Tank::update($param,['id'=>$param['id']]);
            if(!$res){
                throw new \Exception('失败');
            }
            return $this->success([],'编辑成功');
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

    
    public function del($param=[]){
        try{
            
            $res = Tank::destroy($param['ids']);
            if(!$res){
                throw new \Exception('删除失败');
            }
            return $this->success([],'删除成功');
            
        }catch (\Exception $exception){
            $this->recordLog($exception);
            return $this->error();
        }
    }

}
