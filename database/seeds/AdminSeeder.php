<?php

use think\migration\Seeder;

class AdminSeeder extends Seeder
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
        $this->execute('truncate '.$prefix.'admin');
        $data = [
            [
                'id' => 1,
                'username' => 'admin',
                'nickname' => '观海听潮',
                'email' => '1256699215@qq.com',
                'phone' => '15063337229',
                'avatar' => 'https://tva2.sinaimg.cn/crop.497.1137.1422.1422.50/c30cc30bjw8eyz97ez2exj21hc1z41kx.jpg',
                'password' => '9f2dbb3349f88244ba106504f110cd91',
                'halt' => 'r4eftf7bsq3hv7t432k574j53v4jq4pe',
                'status' => 2,
                'create_time'=>date('Y-m-d H:i:s'),
                'update_time'=>date('Y-m-d H:i:s')
            ]
        ];
        $this->table('admin')->insert($data)->save();
    }
}
