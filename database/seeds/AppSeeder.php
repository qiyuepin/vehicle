<?php

use think\migration\Seeder;

class AppSeeder extends Seeder
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
        $this->execute('truncate '.$prefix.'app');
        $data = [
            [
                'id' => 1,
                'app_id' => 'ty9fd2848a039ab554',
                'app_secret' => 'ec32286d0718118861afdbf6e401ee81',
                'name' => '后台管理应用',
                'desc' => '',
                'status' => 2,
                'create_time'=>date('Y-m-d H:i:s'),
                'update_time'=>date('Y-m-d H:i:s')
            ]
        ];
        $this->table('app')->insert($data)->save();
    }
}
