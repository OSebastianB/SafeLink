<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('safelink.admin_email', 'admin@safelink.test');
        $password = config('safelink.admin_password');

        if (! $password) {
            // Generate a one-time password for local bootstrap when env is empty.
            $password = 'Admin@'.Str::upper(Str::random(8)).'!';
            $this->command?->warn('SAFELINK_ADMIN_PASSWORD tidak disetel. Password admin sementara: '.$password);
            $this->command?->warn('Segera ganti password setelah login pertama.');
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => config('safelink.admin_name', 'Administrator'),
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Demo user (non-admin) for testing role isolation.
        User::query()->updateOrCreate(
            ['email' => 'user@safelink.test'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
            ]
        );
    }
}
