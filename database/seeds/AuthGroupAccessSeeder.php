<?php

use think\migration\Seeder;

class AuthGroupAccessSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run():void
    {
        $prefix = config('database.connections.mysql.prefix');
        $this->execute('truncate '.$prefix.'auth_group_access');
        $data = [
            [
                'id' => 1,
                'uid' => 1,
                'group_id' => 1,
                'create_time'=>date('Y-m-d H:i:s'),
                'update_time'=>date('Y-m-d H:i:s')
            ]
        ];
        $this->table('auth_group_access')->insert($data)->save();
    }
}
