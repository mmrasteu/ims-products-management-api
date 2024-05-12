<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $fillable = [
        'name',
        'cif',
        'description',
        'email',
        'phone',
        'address',
        'location',
        'zip_code',
        'contact_name',
        'contact_title',
        'notes'
    ];

    // 1:n Relationship (category-products)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}