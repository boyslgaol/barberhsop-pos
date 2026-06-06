<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate tables
        User::truncate();
        Category::truncate();
        Service::truncate();
        Customer::truncate();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Create admin user
        User::create([
            'name' => 'Admin Barbershop',
            'email' => 'admin@barbershop.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true
        ]);
        
        // Create cashier
        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@barbershop.com',
            'password' => Hash::make('kasir123'),
            'role' => 'cashier',
            'is_active' => true
        ]);
        
        // Create barbers
        $barbers = ['Bambang', 'Sutrisno', 'Joko'];
        foreach ($barbers as $barber) {
            User::create([
                'name' => $barber,
                'email' => strtolower($barber) . '@barbershop.com',
                'password' => Hash::make('barber123'),
                'role' => 'barber',
                'is_active' => true
            ]);
        }
        
        // Categories
        $categories = [
            ['name' => 'Potong Rambut', 'slug' => 'potong-rambut', 'sort_order' => 1],
            ['name' => 'Pangkas Jenggot', 'slug' => 'pangkas-jenggot', 'sort_order' => 2],
            ['name' => 'Perawatan', 'slug' => 'perawatan', 'sort_order' => 3],
            ['name' => 'Paket Hemat', 'slug' => 'paket-hemat', 'sort_order' => 4],
        ];
        
        foreach ($categories as $cat) {
            Category::create($cat);
        }
        
        // Services
        $services = [
            // Potong Rambut (category_id = 1)
            ['category_id' => 1, 'name' => 'Potong Rambut Biasa', 'price' => 35000, 'duration' => 30],
            ['category_id' => 1, 'name' => 'Potong Rambut + Cuci', 'price' => 50000, 'duration' => 45],
            ['category_id' => 1, 'name' => 'Potong Rambut Anak', 'price' => 30000, 'duration' => 25],
            ['category_id' => 1, 'name' => 'Potong Rambut + Creambath', 'price' => 80000, 'duration' => 75],
            
            // Pangkas Jenggot (category_id = 2)
            ['category_id' => 2, 'name' => 'Pangkas Jenggot', 'price' => 25000, 'duration' => 20],
            ['category_id' => 2, 'name' => 'Shaving + Masker', 'price' => 40000, 'duration' => 30],
            ['category_id' => 2, 'name' => 'Grooming Lengkap', 'price' => 60000, 'duration' => 45],
            
            // Perawatan (category_id = 3)
            ['category_id' => 3, 'name' => 'Creambath', 'price' => 45000, 'duration' => 40],
            ['category_id' => 3, 'name' => 'Hair Mask', 'price' => 55000, 'duration' => 50],
            ['category_id' => 3, 'name' => 'Facial Pria', 'price' => 75000, 'duration' => 60],
            
            // Paket Hemat (category_id = 4)
            ['category_id' => 4, 'name' => 'Paket Silver', 'price' => 55000, 'duration' => 50],
            ['category_id' => 4, 'name' => 'Paket Gold', 'price' => 90000, 'duration' => 90],
            ['category_id' => 4, 'name' => 'Paket Platinum', 'price' => 150000, 'duration' => 120],
        ];
        
        foreach ($services as $service) {
            Service::create([
                'category_id' => $service['category_id'],
                'name' => $service['name'],
                'code' => Service::generateCode(),
                'price' => $service['price'],
                'duration' => $service['duration'],
                'is_active' => true
            ]);
        }
        
        // Sample customers
        $customers = [
            ['name' => 'Budi Santoso', 'phone' => '081234567890', 'points' => 150, 'total_spent' => 1500000],
            ['name' => 'Andi Wijaya', 'phone' => '081234567891', 'points' => 500, 'total_spent' => 5000000],
            ['name' => 'Cahyo Nugroho', 'phone' => '081234567892', 'points' => 1000, 'total_spent' => 10000000],
        ];
        
        foreach ($customers as $cust) {
            Customer::create([
                'name' => $cust['name'],
                'phone' => $cust['phone'],
                'member_code' => Customer::generateMemberCode(),
                'points' => $cust['points'],
                'total_spent' => $cust['total_spent'],
                'member_level' => 'regular',
                'visit_count' => rand(1, 20),
                'last_visit' => now()->subDays(rand(1, 30))
            ]);
        }
        
        // Update member levels
        foreach (Customer::all() as $customer) {
            $customer->updateMemberLevel();
        }
    }
}