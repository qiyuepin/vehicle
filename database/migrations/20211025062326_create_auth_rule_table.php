<?php

use think\migration\Migrator;
use Phinx\Db\Adapter\MysqlAdapter; //如创建MYSQL特有字段，需导入该命名空间

class CreateAuthRuleTable extends Migrator
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
        $table = $this->table('auth_rule',array('collation'=>'utf8mb4_general_ci','comment' => '规则表--czg'));
        //字段
        $table->addColumn('name','string',['limit'=>80,'default'=>'','null'=>false,'comment'=>'规则唯一标识'])
            ->addColumn('title','string',['limit'=>80,'default'=>'','null'=>false,'comment'=>'规则中文名称'])
            ->addColumn('condition','string',['limit'=>100,'default'=>'','null'=>false,'comment'=>'规则表达式，为空表示存在就验证，不为空表示按照条件验证'])
            ->addColumn('type','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>1,'comment'=>'表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。'])
            ->addColumn('path','string',['limit'=>50,'default'=>'','null'=>false,'comment'=>'路径'])
            ->addColumn('route','string',['limit'=>50,'default'=>'','null'=>false,'comment'=>'路由地址'])
            ->addColumn('pid','integer',['limit'=>11,'default'=>0,'null'=>false,'comment'=>'上级id'])
            ->addColumn('icon','string',['limit'=>20,'default'=>'','null'=>false,'comment'=>'图标'])
            ->addColumn('sort','integer',['limit'=>11,'default'=>0,'null'=>false,'comment'=>'排序'])
            ->addColumn('component','string',['limit'=>20,'default'=>'','null'=>false,'comment'=>'组件'])
            ->addColumn('redirect','string',['limit'=>20,'default'=>'','null'=>false,'comment'=>'重定向页面'])
            ->addColumn('menu','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>1,'comment'=>'1导航 2菜单（按钮）'])
            ->addColumn('always_show','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>0,'comment'=>'1= 总显示,0=否 依据子导航个数'])
            ->addColumn('status','integer',['limit'=>MysqlAdapter::INT_TINY,'null'=>false,'default'=>1,'comment'=>'状态：为1正常，为0禁用'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //索引
            ->addIndex(['name'], ['unique' => true])
            //创建
            ->save();
    }
}
