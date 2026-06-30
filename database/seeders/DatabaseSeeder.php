<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DemoUserSeeder::class,
            DemoSkillSeeder::class,
            DemoGroupSeeder::class,
            DemoMessageSeeder::class,
            DemoFeedbackSeeder::class,
            DemoReportSeeder::class,
            DemoNotificationSeeder::class,
        ]);
    }
}
