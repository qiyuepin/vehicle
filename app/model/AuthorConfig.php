<?php


namespace app\model;

use think\Model;

/**
 * AuthorConfig
 * created on 2021/7/9 17:24
 * created by chengzhigang
 */
class AuthorConfig extends Model
{
    // 设置数据表（不含前缀）
    protected $name = 'author_config';
    protected $autoWriteTimestamp = 'datetime';

    public function getLabelAttr($value){
        return explode("|",$value);
    }
    public function setLabelAttr($value)
    {
        return implode("|",$value);
    }

}
