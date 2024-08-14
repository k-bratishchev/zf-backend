<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }

    public function configure() : OrderFactory
    {
        return $this->afterCreating(function (Order $order) {
            $products = Product::inRandomOrder()->take(3)->get();
            $order->products()->saveMany($products);
            $productsData = [];
            $total_price = 0;
            foreach ($products as $product) {
                $quantity = $this->faker->numberBetween(1, 5);
                $productsData[] = [
                    'product_id' => $product->id,
                    'unit_price' => $product->price,
                    'quantity' => $quantity
                ];

                $total_price += $product->price * $quantity;
            }

            $order->products()->sync($productsData);
            $order->total_price = $total_price;

            $order->save();
        });
    }
}
