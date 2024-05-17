<?php

namespace Database\Seeders;

use App\Api\Models\ServerModel;
use App\Api\Models\MaintainNoticeModel;
use App\Api\Models\ImpeachModel;
use App\Api\Models\SensitiveWordDataModel;
use Illuminate\Database\Seeder;

class ApiSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {

        ServerModel::truncate();
        ServerModel::insert(
            [

            ]
        );

        MaintainNoticeModel::truncate();
        MaintainNoticeModel::insert(
            [

            ]
        );

        ImpeachModel::truncate();
        ImpeachModel::insert(
            [

            ]
        );

        SensitiveWordDataModel::truncate();
        SensitiveWordDataModel::insert(
            [

            ]
        );
    }
}
