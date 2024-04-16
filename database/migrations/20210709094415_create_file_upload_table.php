<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateFileUploadTable extends Migrator
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
        $table = $this->table('file_upload',array('collation'=>'utf8mb4_general_ci','comment' => '文件上传表--czg'));
        //字段
        $table->addColumn('name','string',['limit'=>100,'null'=>false,'comment'=>'文件名称'])
            ->addColumn('path','string',['limit'=>255,'null'=>false,'comment'=>'文件相对路径'])
            ->addColumn('pathurl','string',['limit'=>255,'null'=>false,'comment'=>'绝对路径'])
            ->addColumn('size','integer',['limit'=>11,'null'=>false,'comment'=>'文件大小'])
            ->addColumn('type','string',['limit'=>50,'null'=>false,'comment'=>'文件类型'])
            ->addColumn('md5','string',['limit'=>50,'null'=>false,'comment'=>'文件md5值'])
            ->addColumn('create_time', 'datetime',['default' => 'CURRENT_TIMESTAMP','update' => ''])
            ->addColumn('update_time', 'datetime',['null'=>true])
            //创建
            ->addIndex(['md5'], ['name' => 'md5'])
            ->save();
    }
}
