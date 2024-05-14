<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'categories' => $categories,
            'status' => 200
        ], 200);
    }

    public function filter(Request $request)
    {
        // Define validation rules for filter fields
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string|nullable',
            'parent_id' => 'numeric|exists:categories,id',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error validating filter data',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Start a categories query
        $query = Category::query();

        // Apply filters if present in the request
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        // Execute the query and get filtered categories
        $filteredCategories = $query->get();

        // Return filtered categories in JSON format
        return response()->json([
            'message' => 'Filtered categories retrieved successfully',
            'categories' => $filteredCategories,
            'status' => 200
        ], 200);
    }

    /**
     * Display a tree of the resource.
     */
    public function indexTree()
    {
        // Get all root categories
        $rootCategories = Category::with('children')->whereNull('parent_id')->get();
        
        // Transform root categories using the CategoryResource
        $categories_tree = CategoryResource::collection($rootCategories);

        return response()->json([
            'categories_tree' => $categories_tree,
            'status' => 200
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        // Validate that the request contains only allowed fields
        $allowedFields = ['name', 'description', 'parent_id'];

        // Check if the request contains any additional fields
        $extraFields = array_diff(array_keys($request->all()), $allowedFields);
        if (!empty($extraFields)) {
            return response()->json([
                'message' => 'The request contains invalid fields.',
                'extra_fields' => $extraFields,
                'status' => 400
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',  
            'description' => 'nullable|string', 
            'parent_id' => 'nullable|numeric|exists:categories,id',
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => 'Error validating data',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        if (!is_null($request->parent_id)) {
            $categoryExists = Category::where('id', $request->parent_id)->exists();
        
            if (!$categoryExists) {
                return response()->json([
                    'message' => 'The referenced parent category does not exist in the database.',
                    'status' => 400
                ], 400);
            }
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);

        if (!$category) {
            return response()->json([
                'message' => 'Error creating category',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'category' => $category,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status' => 404
            ], 404);
        }


        return response()->json([
            'category' => $category,
            'status' => 200
        ], 200);
    }

    public function showProducts($id)
    {
        // Find the category by its ID
        $category = Category::find($id);

        // If the category does not exist, return an error
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status' => 404
            ], 404);
        }

        // Get all products associated with this category
        $products = $category->products()->get();

        // Return the products associated with the category in JSON format
        return response()->json([
            'message' => 'Products associated with the category retrieved successfully',
            'products' => $products,
            'status' => 200
        ], 200);
    }

    public function showTree(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json( [
                'message' => 'Category not found',
                'status' => 404
            ], 404);
        }

        $category_tree = new CategoryResource($category);

        return response()->json([
            'category_tree' => $category_tree,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {   
        // Find the category by its ID and update it with the new values
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error validating data',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        
        if ($request->has('parent_id')) {
            // Check if the new parent_id is equal to the category ID or to any of its descendants
            if ($request->parent_id == $category->id || $category->isDescendantOf($request->parent_id)) {
                // Return a response with the updated category
                return response()->json([
                    'message' => 'The new parent_id cannot be the ID of the category or one of its descendants',
                    'status' => 400
                ], 400);
            }
        }

        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
            'parent_id'   => $request->parent_id,
        ]);

        // Return a response with the updated category
        return response()->json([
            'message' => 'Category updated',
            'category' => $category,
            'status' => 200
        ], 200);
    }

    public function updatePartial(Request $request, $id)
    {
        // Find the category by its ID
        $category = Category::find($id);

        // If the category does not exist, return an error
        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status' => 404
            ], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|numeric|exists:categories,id'
        ]);

        // If validation fails, return an error
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error validating data',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Update only the fields present in the request
        $category->fill($validator->validated());
        $category->save();

        // Return a response with the updated category
        return response()->json([
            'message' => 'Category updated',
            'category' => $category,
            'status' => 200
        ], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        $category = Category::find($id);

        if (!$category) {
            $data = [
                'message' => 'Category not found',
                'status' => 404
            ];
    
            return response()->json($data, 404);
        }

        $descendants = $category->descendants();

        if(!$descendants->isEmpty()) {
            return response()->json([
                'message' => 'Cannot delete a category with children',
                'status' => 400
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
            'status' => 200
        ], 200);

    }
}
