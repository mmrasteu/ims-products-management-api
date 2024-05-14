<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = [
        'name',  
        'description', 
        'parent_id',
    ];

    // 1:n Relationship (category-products)
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function descendants()
    {
        // Initialize a collection to store the descendants
        $descendants = collect();

        // Get all descendants recursively
        $this->appendDescendants($descendants);

        return $descendants;
    }

    protected function appendDescendants(&$descendants)
    {
        // Get all children of this category
        $children = $this->children;

        // Iterate over children and add them to the descendants collection
        foreach ($children as $child) {
            $descendants->push($child);
            $child->appendDescendants($descendants);
        }
    }

    public function isDescendantOf($parent_id)
    {
        // Get the current category
        $category = Category::find($this->id);

        // Get all descendants of the category
        $descendants = $category->descendants();

        // Check if $parent_id is in the descendants collection
        return $descendants->contains('id', $parent_id);
    }

}