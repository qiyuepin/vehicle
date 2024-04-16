<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter; //如创建MYSQL特有字段，需导入该命名空间
class CreateAdvertTable extends Migrator
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
        $table = $this->table('advert',array('collation'=>'utf8mb4_general_ci','comment' => '广告表--czg'));
        //字段
        $table->addColumn('title','string',['limit'=>20,'null'=>false,'comment'=>'标题'])
            ->addColumn('position','integer',['limit'=>MysqlAdapter::INT_TINY,'default'=>1,'comment'=>'广告位'])->addIndex('position')
            ->addColumn('thumb','string',['limit'=>255,'null'=>false,'comment'=>'图片'])
            ->addColumn('sort','integer',['limit'=>11,'null'=>false,'default'=>0,'comment'=>'排序'])
            ->addColumn('status','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>2,'comment'=>'1禁用 2启用'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //创建
            ->save();
    }
}
