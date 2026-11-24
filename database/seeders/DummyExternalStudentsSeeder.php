<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DummyExternalStudentsSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('dummy:external-students', [
            'count' => 2000,
            '--bc' => 180,
        ]);
    }
}
