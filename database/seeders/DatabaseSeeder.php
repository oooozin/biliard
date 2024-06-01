<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            RoleHasPermissionSeeder::class,

            ShopSeeder::class,
            SuperAdminSeeder::class,
            // CategorySeeder::class,
            
            // CashierSeeder::class,
            // TableNumberSeeder::class,
            // CustomerSeeder::class,
            // PrinterSeeder::class,
        ]);

        // for($i = 0; $i < 100000; $i++){
        //     // $this->call([InvoiceSeeder::class]);
        //     $this->call([TableNumberSeeder::class]);
        // };

        // \App\Models\User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        // ]);
    }
}