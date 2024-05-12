<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::all();

        $data = [
            'products' => $suppliers,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function filter(Request $request)
    {
        // Definir reglas de validación para los campos de filtro
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'cif' => 'string|max:9',
            'description' => 'string|nullable',
            'email' => 'email|nullable',
            'phone' => 'numeric|nullable',
            'address' => 'string|nullable',
            'location' => 'string|nullable',
            'zip_code' => 'numeric|nullable',
            'contact_name' => 'string|nullable',
            'contact_title' => 'string|nullable',
            'notes' => 'string|nullable',
        ]);

        // Manejar errores de validación
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos del filtro',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Iniciar una consulta de proveedores
        $query = Supplier::query();

        // Aplicar filtros si están presentes en la solicitud
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('cif')) {
            $query->where('cif', 'like', '%' . $request->input('cif') . '%');
        }

        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->input('description') . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', $request->input('email'));
        }

        if ($request->filled('phone')) {
            $query->where('phone', $request->input('phone'));
        }

        if ($request->filled('address')) {
            $query->where('address', 'like', '%' . $request->input('address') . '%');
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->filled('zip_code')) {
            $query->where('zip_code', $request->input('zip_code'));
        }

        if ($request->filled('contact_name')) {
            $query->where('contact_name', 'like', '%' . $request->input('contact_name') . '%');
        }

        if ($request->filled('contact_title')) {
            $query->where('contact_title', 'like', '%' . $request->input('contact_title') . '%');
        }

        if ($request->filled('notes')) {
            $query->where('notes', 'like', '%' . $request->input('notes') . '%');
        }

        // Ejecutar la consulta y obtener los proveedores filtrados
        $filteredSuppliers = $query->get();

        // Devolver los proveedores filtrados en formato JSON
        return response()->json([
            'message' => 'Proveedores filtrados obtenidos correctamente',
            'suppliers' => $filteredSuppliers,
            'status' => 200
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar que la solicitud solo contenga los campos permitidos
        $allowedFields = [
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
            'name'                  => 'required|string|max:255',  
            'cif'                   => 'required|string|max:9',
            'description'           => 'nullable|string', 
            'email'                 => 'required|email',
            'phone'                 => 'nullable|number',
            'address'               => 'nullable|string',
            'location'              => 'nullable|string',
            'zip_code'              => 'nullable|number',
            'contact_name'          => 'nullable|string',
            'contact_title'         => 'nullable|string',
            'notes'                 => 'nullable|string'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Insertamos los datos
        $supplier = Supplier::create([
            'name'                  => $request->name,
            'cif'                   => $request->cif,
            'description'           => $request->description,
            'email'                 => $request->email,
            'phone'                 => $request->phone,
            'address'               => $request->address,
            'location'              => $request->location,
            'zip_code'              => $request->zip_code,
            'contact_name'          => $request->contact_name,
            'contact_title'         => $request->contact_title,
            'notes'                 => $request->notes
        ]);

        if (!$supplier) {
            return response()->json([
                'message' => 'Error al crear el proveedor',
                'status' => 500
            ], 500);
        }

        return response()->json([
            'supplier' => $supplier,
            'status' => 201
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'message' => 'Proveedor no encontrado',
                'status' => 404
            ], 404);
        }

        return response()->json([
            'supplier' => $supplier,
            'status' => 200
        ], 200);
    }

    public function showProducts($id)
    {
        // Buscar el proveedor por su ID
        $supplier = Supplier::find($id);

        // Si el proveedor no existe, devolver un error
        if (!$supplier) {
            return response()->json([
                'message' => 'Proveedor no encontrado',
                'status' => 404
            ], 404);
        }

        // Obtener todos los productos asociados a este proveedor
        $products = $supplier->products()->get();

        // Devolver los productos asociados al proveedor en formato JSON
        return response()->json([
            'message' => 'Productos asociados al proveedor obtenidos correctamente',
            'products' => $products,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {   
        // Buscar el proveedor por su ID y actualizarla con los nuevos valores
        $supplier = Supplier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'                  => 'string|max:255',  
            'cif'                   => 'string|max:9',
            'description'           => 'nullable|string', 
            'email'                 => 'nullable|email',
            'phone'                 => 'nullable|number',
            'address'               => 'nullable|string',
            'location'              => 'nullable|string',
            'zip_code'              => 'nullable|number',
            'contact_name'          => 'nullable|string',
            'contact_title'         => 'nullable|string',
            'notes'                 => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        $supplier->update([
            'name'                  => $request->name,
            'cif'                   => $request->cif,
            'description'           => $request->description,
            'email'                 => $request->email,
            'phone'                 => $request->phone,
            'address'               => $request->address,
            'location'              => $request->location,
            'zip_code'              => $request->zip_code,
            'contact_name'          => $request->contact_name,
            'contact_title'         => $request->contact_title,
            'notes'                 => $request->notes
        ]);

        // Devolver una respuesta con el proveedor actualizada
        return response()->json([
            'message' => 'Proveedor actualizado',
            'supplier' => $supplier,
            'status' => 200
        ], 200);
    }

    public function updatePartial(Request $request, $id)
    {
        // Buscar el proveedor por su ID
        $supplier = Supplier::find($id);

        // Si el proveedor no existe, devolver un error
        if (!$supplier) {
            return response()->json([
                'message' => 'Proveedor no encontrado',
                'status' => 404
            ], 404);
        }

        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',  
            'cif'                   => 'required|string|max:9',
            'description'           => 'nullable|string', 
            'email'                 => 'nullable|email',
            'phone'                 => 'nullable|number',
            'address'               => 'nullable|string',
            'location'              => 'nullable|string',
            'zip_code'              => 'nullable|number',
            'contact_name'          => 'nullable|string',
            'contact_title'         => 'nullable|string',
            'notes'                 => 'nullable|string'
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
        $supplier->fill($validator->validated());
        $supplier->save();

        // Devolver una respuesta con el proveedor actualizada
        return response()->json([
            'message' => 'Proveedor actualizado',
            'supplier' => $supplier,
            'status' => 200
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        $supplier = Supplier::find($id);

        if (!$supplier) {
            $data = [
                'message' => 'Proveedor no encontrado',
                'status' => 404
            ];
    
            return response()->json($data, 404);
        }

        $supplier->delete();

        return response()->json([
            'message' => 'Proveedor eliminado correctamente',
            'status' => 200
        ], 200);

    }
}
