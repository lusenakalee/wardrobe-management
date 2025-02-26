<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thread>
 */
class ThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Creates a new user if not provided
            'name' => $this->faker->word() . ' ' . $this->faker->randomElement(['Shirt', 'Pants', 'Shoes', 'Jacket']),
            'category' => $this->faker->randomElement(['Shirt', 'Pants', 'Shoes', 'Jacket', 'Hat', 'Accessories']),
            'color' => $this->faker->safeColorName(),
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
            'season' => $this->faker->randomElement(['Summer', 'Winter', 'All-Season', null]),
            'brand' => $this->faker->randomElement(['Nike', 'Adidas', 'Zara', 'H&M', 'Gucci', null]),
            'description' => $this->faker->sentence(),
            'image_url' => $this->faker->imageUrl(200, 200, 'fashion', true), // Generates a random image URL
            'price' => $this->faker->optional()->randomFloat(2, 5, 500), // Price between $5 and $500


            // Ownership Details
            'ownership_status' => $this->faker->randomElement(['owned', 'rented', 'borrowed']),
            'rental_due_date' => $this->faker->optional()->date(),
            'borrowed_from' => $this->faker->optional()->name(),
            'rented_from' => $this->faker->optional()->company(),


            // Additional Fields
            'tags' => json_encode([$this->faker->word(), $this->faker->word()]),
            'condition' => $this->faker->randomElement(['new', 'good', 'worn out', 'needs repair']),
            'laundry_status' => $this->faker->randomElement(['clean', 'dirty', 'dry cleaning', 'lost']),
            'wear_count' => $this->faker->numberBetween(0, 50),
            'last_worn_date' => $this->faker->optional()->date(),


            'created_at' => now(),
            'updated_at' => now(),
        ];

    }
}
