<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Database\Seeders\RoleSeeder;
use Database\Seeders\StaffSeeder;
use Database\Seeders\GenderSeeder;
use Database\Seeders\FeatureSeeder;
use Database\Seeders\DivisionSeeder;
use Database\Seeders\TownshipSeeder;
use Database\Seeders\InventorySeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\NrcTownshipSeeder;
use Database\Seeders\ComplaintCategorySeeder;
use Database\Seeders\ExtraTagSeeder;
use Termwind\Components\Hr;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // FeatureSeeder::class,//important
            // DivisionSeeder::class,//important
            // TownshipSeeder::class,//important
            // GenderSeeder::class,//important
            // NrcTownshipSeeder::class,//important
            // StaffSeeder::class,//important
            // AreaCategorySeeder::class,//important
            // HeadAccountSeeder::class, //important
            // SubAccountSeeder::class,//important
            // MenuCategorySeeder::class, //important
            // AccessoryCategorySeeder::class, //important
            // ServiceCategorySeeder::class, //important
            // GpsSeeder::class,

            // ComplaintCategorySeeder::class,
            // // InventorySeeder::class,
            // FeatureSeeder::class,
            // DepartmentSeeder::class,
            // // AreaCategorySeeder::class, //tem command
            // AreaSeeder::class,
            // RoleSeeder::class,
            // GenderSeeder::class,
            // AccessoryCategorySeeder::class,
            // MenuCategorySeeder::class,
            // ServiceCategorySeeder::class,
            // HeadAccountSeeder::class,
            // SubAccountSeeder::class,
            // AccountTableSeeder::class,
            // // RoomAndTableSeeder::class,
            // InventorySeeder::class,
            // StaffSeeder::class,
            // GpsSeeder::class,
            // DepartmentFeatureSeeder::class,
            // NrcTownshipSeeder::class,
            // ExtraTagSeeder::class,
            // OffDaySettingSeeder::class,

            //hr seeder 
            ComplaintCategorySeeder::class,
            // FeatureSeeder::class,
            HrFeatureSeeder::class,
            DepartmentSeeder::class,
            AreaSeeder::class,
            RoleSeeder::class,
            GenderSeeder::class,
            StaffSeeder::class,
            GpsSeeder::class,
            DepartmentFeatureSeeder::class,
            NrcTownshipSeeder::class, //important
        ]);
    }
}
