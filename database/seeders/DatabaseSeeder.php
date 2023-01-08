<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()
            ->has(
                Order::factory(3)
                ->hasAttached(Product::factory()->count(3), ['quantity' => fake()->randomNumber(1)])
                ->count(3)
            )
            ->create();
    }
}
