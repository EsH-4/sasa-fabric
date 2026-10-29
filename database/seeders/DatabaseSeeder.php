<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@sasafabric.test'],
            [
                'name' => 'Admin Sasa',
                'password' => 'password',
            ]
        );

        $this->call(SiteContentSeeder::class);
    }
}
