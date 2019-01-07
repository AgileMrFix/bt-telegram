<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name'=>'super_admin'
            ],
            [
                'name'=>'admin'
            ],
            [
                'name'=>'lead'
            ],

        ];

        foreach ($data as $datum)
            \App\Models\Telegram\Role::create($datum);
    }
}
