<?php
declare (strict_types = 1);

namespace app\traits;
use app\model\FileUpload;
use think\facade\Filesystem;

/**
 * UploadTrait
 * created on 2021/11/4 8:57
 * created by chengzhigang
 */
trait UploadTrait
{

    /**
     * @desc 文件上传
     * @method upload
     * @param $file
     * @param $type
     * @param $config
     * @return array
     * @author chengzhigang
     * @time 2021/11/4 10:51
     */
    public function upload($file,$type,$config=[]){
        // dump($file);die;
        try{
            if($file->isValid()){
                $driver = config('filesystem.default');
                $md5 = $file->hash('md5');
                $fileName = $file->getOriginalName();
                $configs = config('filesystem.type');
                $defaultConfig = $configs[$type];
                $mimeType = $file->getOriginalMime();
                $size = $file->getSize();
                $fileExt = $config['ext'] ?? $defaultConfig['ext'];
                $fileSize = $config['size'] ?? $defaultConfig['size'];
                $fileDir = $config['dir'] ?? $defaultConfig['dir'];
                // 使用验证器验证上传的文件
                validate(['file' => [
                    // 限制文件大小(单位b)，这里限制为4M
                    'fileSize' => $fileSize,
                    // 限制文件后缀，多个后缀以英文逗号分割
                    'fileExt'  => $fileExt
                ]])->check(['file' => $file]);
                //验证是否上传过
                $uploadData = FileUpload::where('md5',$md5)->find();
                if($uploadData){
                    return ['code'=>200,'msg'=>'上传成功','data'=>['url'=>$uploadData['pathurl']]];
                }
                
                if($driver=='local'||$driver=='public'){
                    //上传本地
                    $realPath = Filesystem::disk('public')->putFile($fileDir,$file);
                    $path = str_replace("\\","/",'/storage/'.$realPath);
                    $pathUrl = request()->domain() . $path;
                }elseif($driver=='qiniu'){
                    return ['code'=>500,'msg'=>'暂不支持第三方平台上传','data'=>[]];
                }
                $this->addRecord($fileName,$path,$pathUrl,$size,$mimeType,$md5);
                return ['code'=>200,'msg'=>'上传成功','data'=>['url'=>$pathUrl]];
            }
        }catch (\Exception $exception){
            return ['code' => 500, 'msg' => $exception->getMessage(), 'data' => []];
        }
    }

    /**
     * @desc 删除文件
     * @method deleteFile
     * @param $path
     * @return bool
     * @author chengzhigang
     * @time 2022/11/2 8:50
     */
    public function deleteFile($file):bool {
        $path = app()->getRootPath() . 'public' . $file['path'];//本地相对地址
        if(file_exists($path)){
            return @unlink($path);
        }
        return false;
    }

    //记录上传
    public function addRecord($name,$path,$pathurl,$size,$type,$md5){
        FileUpload::create([
            'name' => $name,
            'path' => $path,
            'pathurl' => $pathurl,
            'size' => $size,
            'type' => $type,
            'md5'=> $md5
        ]);
    }
}
