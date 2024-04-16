<?php


namespace app\model;

use think\Model;

/**
 * FileUpload
 * created on 2021/11/4 9:51
 * created by chengzhigang
 */
class FileUpload extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'file_upload';
    protected $autoWriteTimestamp = 'datetime';
}
