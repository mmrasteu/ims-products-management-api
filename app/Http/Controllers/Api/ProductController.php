<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|max:255', 
            'sku_code'              => 'required|max:255|unique:products', 
            'description'           => 'nullable|string', 
            'price'                 => 'required|numeric', 
            'cost_price'            => 'required|numeric', 
            'status'                => 'required|boolean', 
            'additional_features'   => 'required|json'
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
            'additional_features'   => 'required|json'
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
            'description'           => 'nullable|string', 
            'price'                 => 'numeric', 
            'cost_price'            => 'numeric', 
            'status'                => 'boolean', 
            'additional_features'   => 'json'
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
