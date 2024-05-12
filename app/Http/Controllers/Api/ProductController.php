<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'products' => $products,
            'status' => 200
        ], 200);
    }

    /**
     * Filter products based on criteria.
     */
    public function filter(Request $request)
    {
        // Validar los datos del filtro
        $validator = Validator::make($request->all(), [
            'name'                  => 'max:255', 
            'sku_code'              => 'max:255',
            'description'           => 'string', 
            'price'                 => 'numeric', 
            'cost_price'            => 'numeric', 
            'status'                => 'boolean', 
            'additional_features'   => 'json',
            'category_id'           => 'numeric',
            'supplier_id'           => 'numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos del filtro',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Construir la consulta de productos filtrados
        $query = Product::query();

        // Aplicar filtros
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('sku_code')) {
            $query->where('sku_code', $request->input('sku_code'));
        }

        if ($request->has('description')) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }

        if ($request->has('price')) {
            $query->where('price', $request->input('price'));
        }

        if ($request->has('cost_price')) {
            $query->where('cost_price', $request->input('cost_price'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('additional_features')) {
            $query->where('additional_features', $request->input('additional_features'));
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        // Ejecutar la consulta y obtener los productos filtrados
        $filteredProducts = $query->get();

        return response()->json([
            'message' => 'Productos filtrados obtenidos correctamente',
            'products' => $filteredProducts,
            'status' => 200
        ], 200);
    }

    /**
     * Get product's category
     */
    public function showCategory(string $id){
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        $product_category = Category::find($product->category_id);

        if (!$product_category) {
            return response()->json([
                'message' => 'Categoria no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'product_category' => $product_category,
            'status' => 200
        ], 200);


    }

    /**
     * Get product's supplier
     */
    public function showSupplier(string $id){
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        $product_supplier = Supplier::find($product->supplier_id);

        if (!$product_supplier) {
            return response()->json([
                'message' => 'Proveedor no encontrada',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'product_supplier' => $product_supplier,
            'status' => 200
        ], 200);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|max:255', 
            'sku_code'              => 'required|max:255|unique:products', 
            'description'           => 'required|string', 
            'price'                 => 'required|numeric', 
            'cost_price'            => 'required|numeric', 
            'status'                => 'required|boolean', 
            'additional_features'   => 'required|json',
            'category_id'           => 'required|numeric',
            'supplier_id'           => 'required|numeric'
        ]);

        if ($validator->fails()){
            

            return response()->json([
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $product = Product::create([
            'name'                  => $request->name,
            'sku_code'              => $request->sku_code,
            'description'           => $request->description,
            'price'                 => $request->price,
            'cost_price'            => $request->cost_price,
            'status'                => $request->status,
            'additional_features'   => $request->additional_features,
            'category_id'           => $request->category_id,
            'supplier_id'           => $request->supplier_id
        ]);

        if (!$product) {
            return response()->json([
                'message' => 'Error al crear el producto',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'products' => $product,
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'product' => $product,
            'status' => 200
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'                  => 'required|max:255', 
            'sku_code'              => 'required|max:255|unique:products', 
            'description'           => 'nullable|string', 
            'price'                 => 'required|numeric', 
            'cost_price'            => 'required|numeric', 
            'status'                => 'required|boolean', 
            'additional_features'   => 'required|json',
            'category_id'           => 'required|numeric',
            'supplier_id'           => 'required|numeric'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $product->update([
            'name'                  => $request->name,
            'sku_code'              => $request->sku_code,
            'description'           => $request->description,
            'price'                 => $request->price,
            'cost_price'            => $request->cost_price,
            'status'                => $request->status,
            'additional_features'   => $request->additional_features,
            'category_id'           => $request->category_id,
            'supplier_id'           => $request->supplier_id
        ]);
    
        // Enviar la respuesta JSON
        return response()->json([
            'message' => 'Producto actualizado',
            'product' => $product,
            'status' => 200
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePartial(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'                  => 'max:255', 
            'sku_code'              => 'max:255|unique:products,sku_code,' . $id, // Asegúrate de excluir el ID actual del producto de la regla unique
            'description'           => 'string', 
            'price'                 => 'numeric', 
            'cost_price'            => 'numeric', 
            'status'                => 'boolean', 
            'additional_features'   => 'json',
            'category_id'           => 'numeric',
            'supplier_id'           => 'numeric'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Actualizar el producto con los datos validados
        $product->fill($request->validated());
        $product->save();

        return response()->json([
            'message' => 'Producto actualizado',
            'product' => $product,
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'status' => 404
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado correctamente',
            'status' => 200
        ], 200);
        
    }
}
