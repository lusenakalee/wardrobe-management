<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thread extends Model
{
    use HasFactory;

    protected $table = 'threads';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'color',
        'size',
        'season',
        'brand',
        'description',
        'price',
        'ownership_status',
        'rental_due_date',
        'borrowed_from',
        'rented_from',
        'tags',
        'condition',
        'laundry_status',
        'wear_count',
        'last_worn_date',
        'image_url',
    ];

    protected $casts = [
        'tags' => 'array',
        'price' => 'decimal:2',
        'rental_due_date' => 'date',
        'last_worn_date' => 'date',
        'wear_count' => 'integer',
    ];

    /**
     * Get the user that owns the clothing item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Increment the wear count and update last worn date.
     */
    public function markAsWorn()
    {
        $this->increment('wear_count');
        $this->update(['last_worn_date' => now()]);
    }

    /**
     * Accessor for laundry status (Capitalized).
     */
    protected function laundryStatus(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
        );
    }

    /**
     * Mutator for condition (Lowercase).
     */
    protected function condition(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtolower($value),
        );
    }


    
    
}



