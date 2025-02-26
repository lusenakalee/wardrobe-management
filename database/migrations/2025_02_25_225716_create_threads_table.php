<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();    
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to associate with users
            $table->string('name'); // Name of the clothing item
            $table->string('category'); // Category (e.g., "Shirt", "Pants", "Shoes")
            $table->string('color'); // Color (e.g., "Blue", "Red", "Black")
            $table->string('size'); // Size (e.g., "S", "M", "L", "42")
            $table->string('season')->nullable(); // Season (e.g., "Summer", "Winter", "All-Season")
            $table->string('brand')->nullable(); // Brand (e.g., "Nike", "Zara")
            $table->text('description')->nullable(); // Optional description
            $table->string('image_url')->nullable(); // Image URL
            $table->decimal('price', 10, 2)->nullable(); // Price of the item
    
            // Ownership details
            $table->enum('ownership_status', ['owned', 'rented', 'borrowed'])->default('owned');
            $table->date('rental_due_date')->nullable(); // Due date if rented or borrowed
            $table->string('borrowed_from')->nullable(); // If borrowed, who it was borrowed from
            $table->string('rented_from')->nullable(); // If rented, rental source
    
            // New additions
            $table->json('tags')->nullable(); // Tags for filtering and categorization
            $table->enum('condition', ['new', 'good', 'worn out', 'needs repair'])->default('good'); // Item condition
            $table->enum('laundry_status', ['clean', 'dirty', 'dry cleaning', 'lost'])->default('clean'); // Laundry status
            $table->integer('wear_count')->default(0); // Number of times worn
            $table->date('last_worn_date')->nullable(); // Last worn date
    
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
