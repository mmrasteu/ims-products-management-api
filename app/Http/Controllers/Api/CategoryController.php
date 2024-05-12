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
        // Definir reglas de validación para los campos de filtro
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string|nullable',
            'parent_id' => 'numeric|exists:categories,id',
        ]);

        // Manejar errores de validación
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos del filtro',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Iniciar una consulta de categorías
        $query = Category::query();

        // Aplicar filtros si están presentes en la solicitud
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        // Ejecutar la consulta y obtener las categorías filtradas
        $filteredCategories = $query->get();

        // Devolver las categorías filtradas en formato JSON
        return response()->json([
            'message' => 'Categorías filtradas obtenidas correctamente',
            'categories' => $filteredCategories,
            'status' => 200
        ], 200);
    }

    /**
     * Display a tree of the resource.
     */
    public function indexTree()
    {
        // Obtener todas las categorías principales (categorías raíz)
        $rootCategories = Category::with('children')->whereNull('parent_id')->get();
        
        // Transformar las categorías raíz utilizando el recurso CategoryResource
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
        // Validar que la solicitud solo contenga los campos permitidos
        $allowedFields = ['name', 'description', 'parent_id'];

        // Comprobar si la solicitud contiene algún campo adicional
        $extraFields = array_diff(array_keys($request->all()), $allowedFields);
        if (!empty($extraFields)) {
            return response()->json([
                'message' => 'La solicitud contiene campos erroneos.',
                'extra_fields' => $extraFields,
                'status' => 400
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'name'                  => 'required|max:255',  
            'description'           => 'nullable|string', 
            'parent_id'             => 'nullable|numeric|exists:categories,id',
        ]);

        if (!is_null($request->id_parent)) {
            $request->merge(['parent_id' => (int) $request->parent_id]);
        }
        
        if ($validator->fails()){
            return response()->json([
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        if (!is_null($request->id_parent)) {
            $categoryExists = Category::where('id', $request->id_parent)->exists();
        
            if (!$categoryExists) {
                return response()->json([
                    'message' => 'La categoría padre referenciada no existe en la base de datos.',
                    'status' => 400
                ], 400);
            }
        }

        $category = Category::create([
            'name'                  => $request->name,
            'description'           => $request->description,
            'parent_id'             => $request->parent_id,
        ]);

        if (!$category) {
            return response()->json([
                'message' => 'Error al crear el producto',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'products' => $category,
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
                'message' => 'Categoria no encontrada',
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
    // Buscar la categoría por su ID
    $category = Category::find($id);

    // Si la categoría no existe, devolver un error
    if (!$category) {
        return response()->json([
            'message' => 'Categoría no encontrada',
            'status' => 404
        ], 404);
    }

    // Obtener todos los productos asociados a esta categoría
    $products = $category->products()->get();

    // Devolver los productos asociados a la categoría en formato JSON
    return response()->json([
        'message' => 'Productos asociados a la categoría obtenidos correctamente',
        'products' => $products,
        'status' => 200
    ], 200);
}

    public function showTree(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json( [
                'message' => 'Categoria no encontrada',
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
        // Buscar la categoría por su ID y actualizarla con los nuevos valores
        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }
        
        if ($request->has('parent_id')) {
            // Comprueba si el nuevo parent_id es igual al ID de la categoría o de cualquiera de sus descendientes
            if ($request->parent_id == $category->id || $category->isDescendantOf($request->parent_id)) {
                // Devolver una respuesta con la categoría actualizada
                return response()->json([
                    'message' => 'El nuevo parent_id no puede ser el ID de la categoría o de uno de sus descendientes',
                    'status' => 400
                ], 400);
            }
        }

        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
            'parent_id'   => $request->parent_id,
        ]);

        // Devolver una respuesta con la categoría actualizada
        return response()->json([
            'message' => 'Categoría actualizada',
            'category' => $category,
            'status' => 200
        ], 200);
    }

    public function updatePartial(Request $request, $id)
    {
        // Buscar la categoría por su ID
        $category = Category::find($id);

        // Si la categoría no existe, devolver un error
        if (!$category) {
            return response()->json([
                'message' => 'Categoría no encontrada',
                'status' => 404
            ], 404);
        }

        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'name' => 'max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|numeric|exists:categories,id'
        ]);

        // Si la validación falla, devolver un error
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Actualizar solo los campos presentes en la solicitud
        $category->fill($validator->validated());
        $category->save();

        // Devolver una respuesta con la categoría actualizada
        return response()->json([
            'message' => 'Categoría actualizada',
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
                'message' => 'Categoria no encontrada',
                'status' => 404
            ];
    
            return response()->json($data, 404);
        }

        $descendants = $category->descendants();

        if(!$descendants->isEmpty()) {
            return response()->json([
                'message' => 'No se puede borrar una categoria con hijos',
                'status' => 400
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente',
            'status' => 200
        ], 200);

    }
}
