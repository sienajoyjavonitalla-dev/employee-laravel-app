<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Seed the demo user for coded demo (siena@admin.com / admin123).
     *
     * @return void
     */
    public function run()
    {
        $attrs = [
            'name' => 'Demo Admin',
            'password' => Hash::make('admin123'),
            'roles' => 'admin',
            'abn' => null,
            'rate_per_hour' => null,
            'other_rate_per_hour' => null,
            'ot_rate_per_hour' => null,
            'other_ot_rate_per_hour' => null,
            'travel_allowance' => null,
            'gst' => null,
        ];

        User::updateOrCreate(
            ['email' => 'siena@admin.com'],
            $attrs
        );
    }
}
