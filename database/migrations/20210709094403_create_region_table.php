<?php

use think\migration\Migrator;
use think\migration\db\Column;
use Phinx\Db\Adapter\MysqlAdapter; //如创建MYSQL特有字段，需导入该命名空间
class CreateRegionTable extends Migrator
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
        $table = $this->table('region',array('collation'=>'utf8mb4_general_ci','comment' => '高德省市区表--czg'));
        //字段
        $table->addColumn('adcode','integer',['limit'=>6,'null'=>false,'comment'=>'行政编码'])
            ->addColumn('name','string',['limit'=>50,'null'=>false,'comment'=>'行政名称'])
            ->addColumn('parent','integer',['limit'=>6,'null'=>false,'default'=>0,'comment'=>'上级行政编码'])
            ->addColumn('level','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>1,'comment'=>'行政等级'])
            ->addColumn('citycode','integer',['limit'=>10,'null'=>false,'default'=>0,'comment'=>'城市编码'])
            ->addColumn('center','string',['limit'=>50,'null'=>true,'comment'=>'行政中心'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //创建
            ->addIndex(['adcode'], ['name' => 'adcode'])
            ->addIndex(['parent'], ['name' => 'parent'])
            ->save();
    }
}
