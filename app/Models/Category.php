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

    // Relacion 1:n (category-products)
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
        // Inicializar una colección para almacenar los descendientes
        $descendants = collect();

        // Obtener todos los descendientes de manera recursiva
        $this->appendDescendants($descendants);

        return $descendants;
    }

    protected function appendDescendants(&$descendants)
    {
        // Obtener todos los hijos de esta categoría
        $children = $this->children;

        // Recorrer los hijos y añadirlos a la colección de descendientes
        foreach ($children as $child) {
            $descendants->push($child);
            $child->appendDescendants($descendants);
        }
    }

    public function isDescendantOf($parent_id)
    {
        // Obtener la categoría actual
        $category = Category::find($this->id);

        // Obtener todos los descendientes de la categoría
        $descendants = $category->descendants();

        // Verificar si $parent_id está en la colección de descendientes
        return $descendants->contains('id', $parent_id);
    }

}

