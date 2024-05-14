<?php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="API de Productos",
 *     version="1.0.0",
 *     description="API para gestionar productos",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="Equipo de desarrollo"
 *     )
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Mostrar una lista de productos",
     *     tags={"Productos"},
     *     @OA\Response(response="200", description="Lista de productos"),
     * )
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
     * @OA\Post(
     *     path="/api/products/filter",
     *     summary="Filtrar productos basados en criterios",
     *     tags={"Productos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Producto1"),
     *             @OA\Property(property="sku_code", type="string", example="SKU1234567"),
     *             @OA\Property(property="description", type="string", example="Descripción del producto"),
     *             @OA\Property(property="price", type="number", format="float", example=10.99),
     *             @OA\Property(property="cost_price", type="number", format="float", example=5.99),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="additional_features", type="json", example={"feature1": "value1", "feature2": "value2"}),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="supplier_id", type="integer", example=1),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Productos filtrados"),
     *     @OA\Response(response="400", description="Error de validación"),
     * )
     */
    public function filter(Request $request)
    {
        // Validate filter data
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
                'message' => 'Error validating filter data',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Build filtered products query
        $query = Product::query();

        // Apply filters
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

        // Execute query and get filtered products
        $filteredProducts = $query->get();

        return response()->json([
            'message' => 'Filtered products retrieved successfully',
            'products' => $filteredProducts,
            'status' => 200
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}/category",
     *     summary="Obtener la categoría de un producto por su ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Categoría del producto obtenida con éxito"),
     *     @OA\Response(response="404", description="Producto o categoría no encontrada"),
     * )
     */
    public function showCategory(string $id){
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        $product_category = Category::find($product->category_id);

        if (!$product_category) {
            return response()->json([
                'message' => 'Category not found',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'product_category' => $product_category,
            'status' => 200
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}/supplier",
     *     summary="Obtener el proveedor de un producto por su ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Proveedor del producto obtenido con éxito"),
     *     @OA\Response(response="404", description="Producto o proveedor no encontrado"),
     * )
     */
    public function showSupplier(string $id){
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        $product_supplier = Supplier::find($product->supplier_id);

        if (!$product_supplier) {
            return response()->json([
                'message' => 'Supplier not found',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'product_supplier' => $product_supplier,
            'status' => 200
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Almacenar un nuevo producto en la base de datos",
     *     tags={"Productos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Nuevo Producto"),
     *             @OA\Property(property="sku_code", type="string", example="SKU1234567"),
     *             @OA\Property(property="description", type="string", example="Descripción del nuevo producto"),
     *             @OA\Property(property="price", type="number", format="float", example=10.99),
     *             @OA\Property(property="cost_price", type="number", format="float", example=5.99),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="additional_features", type="json", example={"feature1": "value1", "feature2": "value2"}),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="supplier_id", type="integer", example=1),
     *         ),
     *     ),
     *     @OA\Response(response="201", description="Producto creado con éxito"),
     *     @OA\Response(response="400", description="Error de validación"),
     *     @OA\Response(response="500", description="Error interno del servidor"),
     * )
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
                'message' => 'Error validating data',
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
                'message' => 'Error creating product',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'products' => $product,
            'status' => 201
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Mostrar un producto por su ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Producto obtenido con éxito"),
     *     @OA\Response(response="404", description="Producto no encontrado"),
     * )
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'product' => $product,
            'status' => 200
        ], 200);

    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Actualizar un producto por su ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Producto Actualizado"),
     *             @OA\Property(property="sku_code", type="string", example="SKU1234567"),
     *             @OA\Property(property="description", type="string", example="Descripción del producto actualizada"),
     *             @OA\Property(property="price", type="number", format="float", example=19.99),
     *             @OA\Property(property="cost_price", type="number", format="float", example=9.99),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="additional_features", type="json", example={"feature1": "updated_value1", "feature2": "updated_value2"}),
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(property="supplier_id", type="integer", example=2),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Producto actualizado con éxito"),
     *     @OA\Response(response="400", description="Error de validación"),
     *     @OA\Response(response="404", description="Producto no encontrado"),
     * )
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'                  => 'string|max:255', 
            'sku_code'              => 'string|max:255|unique:products', 
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
                'message' => 'Error validating data',
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
    
        // Return JSON response
        return response()->json([
            'message' => 'Product updated',
            'product' => $product,
            'status' => 200
        ], 200);

    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}",
     *     summary="Actualizar parcialmente un producto por su ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Producto Actualizado"),
     *             @OA\Property(property="sku_code", type="string", example="SKU1234567"),
     *             @OA\Property(property="description", type="string", example="Descripción del producto actualizada"),
     *             @OA\Property(property="price", type="number", format="float", example=19.99),
     *             @OA\Property(property="cost_price", type="number", format="float", example=9.99),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="additional_features", type="json", example={"feature1": "updated_value1", "feature2": "updated_value2"}),
     *             @OA\Property(property="category_id", type="integer", example=2),
     *             @OA\Property(property="supplier_id", type="integer", example=2),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Producto actualizado con éxito"),
     *     @OA\Response(response="400", description="Error de validación"),
     *     @OA\Response(response="404", description="Producto no encontrado"),
     * )
     */
    public function updatePartial(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'                  => 'string|max:255', 
            'sku_code'              => 'string|max:255|unique:products', 
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
                'message' => 'Error validating data',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Update product with validated data
        $product->fill($validator->validated());
        $product->save();

        return response()->json([
            'message' => 'Product updated',
            'product' => $product,
            'status' => 200
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Eliminar un producto por su ID",
     *     tags={"Productos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del producto",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Producto eliminado con éxito"),
     *     @OA\Response(response="404", description="Producto no encontrado"),
     * )
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
                'status' => 404
            ], 404);
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
            'status' => 200
        ], 200);
        
    }
}
