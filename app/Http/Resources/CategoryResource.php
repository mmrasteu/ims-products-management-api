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
            // Create a new category resource for the child and add it to the collection
            $descendants->push(new CategoryResource($child));

            // Recursively call the function to get the descendants of the child
            // and assign them to the 'children' property of the child resource
            $child->children = $this->descendantsRecursive($child);
        }

        return $descendants;
    }

}