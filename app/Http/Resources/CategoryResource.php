<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'children' => CategoryResource::collection($this->descendantsRecursive($this)),
        ];
    }

    private function descendantsRecursive($category)
    {
        $descendants = collect();

        foreach ($category->children as $child) {
            // Crear un nuevo recurso de categoría para el hijo y añadirlo a la colección
            $descendants->push(new CategoryResource($child));

            // Llamar recursivamente a la función para obtener los descendientes del hijo
            // y asignarlos a la propiedad 'children' del recurso del hijo
            $child->children = $this->descendantsRecursive($child);
        }

        return $descendants;
    }

}