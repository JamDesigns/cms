<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
// use Joaopaulolndev\FilamentGeneralSettings\Models\GeneralSetting;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('posts');
        Storage::disk('public')->deleteDirectory('users');
        Storage::disk('public')->makeDirectory('posts');
        Storage::disk('public')->makeDirectory('users');

        $roleSuper = Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'Editor',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'Author',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'Front User',
            'guard_name' => 'web',
        ]);

        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@cms.test',
        ]);

        $user->assignRole($roleSuper);

        Category::create([
            'name' => '{"es":"Sin categoría"}',
            'slug' => '{"es":"sin-categoría"}',
        ]);
    }
}
