<?php

use Illuminate\Database\Seeder;

class DepartmentsSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departments')->truncate();

        $departmetData = [
            [
                'id' => 1,
                'name' => trans('departments.leading'),
                'parent_id' => null
            ],
            [
                'id' => 2,
                'name' => trans('departments.accounting'),
                'parent_id' => 1
            ],
            [
                'id' => 3,
                'name' => trans('departments.hum_resource'),
                'parent_id' => 1
            ],
            [
                'id' => 4,
                'name' => trans('departments.legal'),
                'parent_id' => 1
            ],
            [
                'id' => 5,
                'name' => trans('departments.production'),
                'parent_id' => 1
            ],
            [
                'id' => 6,
                'name' => trans('departments.it'),
                'parent_id' => 1
            ],
            [
                'id' => 7,
                'name' => trans('departments.sale'),
                'parent_id' => 1
            ],
            [
                'id' => 8,
                'name' => trans('departments.electric'),
                'parent_id' => 5
            ],
            [
                'id' => 9,
                'name' => trans('departments.laboratory'),
                'parent_id' => 5
            ],
            [
                'id' => 10,
                'name' => trans('departments.provision'),
                'parent_id' => 5
            ],


        ];

        foreach ($departmetData as $departmetDatum) {
            $dep = new \App\Models\Telegram\Department();
            $dep->id = $departmetDatum['id'];
            $dep->name = $departmetDatum['name'];
            $dep->parent_id = $departmetDatum['parent_id'];
            $dep->save();
        }

    }
}
