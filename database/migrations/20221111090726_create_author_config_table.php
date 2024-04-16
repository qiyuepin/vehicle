<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter; //如创建MYSQL特有字段，需导入该命名空间
class CreateAuthorConfigTable extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        //表
        $table = $this->table('author_config',array('collation'=>'utf8mb4_general_ci','comment' => '作者表--czg'));
        //字段
        $table->addColumn('name','string',['limit'=>20,'null'=>true,'default'=>'','comment'=>'名称'])
            ->addColumn('avatar','string',['limit'=>255,'null'=>true,'default'=>'','comment'=>'头像'])
            ->addColumn('email','string',['limit'=>50,'null'=>true,'default'=>'','comment'=>'邮箱'])
            ->addColumn('phone','string',['limit'=>20,'null'=>true,'default'=>'','comment'=>'手机号'])
            ->addColumn('qq','string',['limit'=>20,'null'=>true,'default'=>'','comment'=>'qq'])
            ->addColumn('address','string',['limit'=>50,'null'=>true,'default'=>'','comment'=>'地址'])
            ->addColumn('position','string',['limit'=>20,'null'=>true,'default'=>'','comment'=>'职位'])
            ->addColumn('speciality','string',['limit'=>100,'null'=>true,'default'=>'','comment'=>'特长'])
            ->addColumn('label','string',['limit'=>200,'null'=>true,'default'=>'','comment'=>'标签'])
            ->addColumn('birthday','date',['null'=>true,'comment'=>'出生日期'])
            ->addColumn('link','string',['limit'=>100,'null'=>true,'default'=>'','comment'=>'链接地址'])
            ->addColumn('signature','string',['limit'=>30,'null'=>true,'default'=>'','comment'=>'签名'])
            ->addColumn('company','string',['limit'=>50,'null'=>true,'default'=>'','comment'=>'公司'])
            ->addColumn('introduction','string',['limit'=>100,'null'=>true,'default'=>'','comment'=>'简介'])
            ->addColumn('gender','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>1,'comment'=>'1男 2女 3保密'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //创建
            ->save();
    }
}
