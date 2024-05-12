<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'sku_code', 
        'description', 
        'price', 
        'cost_price', 
        'status', 
        'additional_features',
        'category_id',
        'supplier_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'status' => 'boolean',
            'additional_features' => 'array',
        ];
    }

    // Relacion 1:n inversa (product-category)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relacion 1:n inversa (product-supplier)
    public function supplier()
    {
        return $this->belongsTo(Category::class);
    }

}
