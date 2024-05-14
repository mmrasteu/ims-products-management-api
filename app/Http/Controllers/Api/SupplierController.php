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
            'suppliers' => $suppliers,
            'status' => 200
        ];

        return response()->json($data, 200);
    }

    public function filter(Request $request)
    {
        // Define validation rules for filter fields
        $validator = Validator::make($request->all(), [
            'name'                  => 'string|max:255',
            'cif'                   => 'string|max:9',
            'description'           => 'string|nullable',
            'email'                 => 'email|nullable',
            'phone'                 => 'nullable|numeric|min_digits:9|max_digits:10',
            'address'               => 'string|nullable',
            'location'              => 'string|nullable',
            'zip_code'              => 'numeric|nullable',
            'contact_name'          => 'string|nullable',
            'contact_title'         => 'string|nullable',
            'notes'                 => 'string|nullable'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error validating filter data',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Start a suppliers query
        $query = Supplier::query();

        // Apply filters if present in the request
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('cif')) {
            $query->where('cif', $request->input('cif'));
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
            $query->where('address', $request->input('address'));
        }

        if ($request->filled('location')) {
            $query->where('location', $request->input('location'));
        }

        if ($request->filled('zip_code')) {
            $query->where('zip_code', $request->input('zip_code'));
        }

        if ($request->filled('contact_name')) {
            $query->where('contact_name', $request->input('contact_name'));
        }

        if ($request->filled('contact_title')) {
            $query->where('contact_title', $request->input('contact_title'));
        }

        if ($request->filled('notes')) {
            $query->where('notes', 'like', '%' . $request->input('notes') . '%');
        }

        // Execute the query and get filtered suppliers
        $filteredSuppliers = $query->get();

        // Return filtered suppliers in JSON format
        return response()->json([
            'message' => 'Filtered suppliers retrieved successfully',
            'suppliers' => $filteredSuppliers,
            'status' => 200
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate that the request contains only allowed fields
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
            'name'                  => 'required|string|max:255',  
            'cif'                   => 'required|string|max:9',
            'description'           => 'nullable|string', 
            'email'                 => 'required|email',
            'phone'                 => 'nullable|numeric|min_digits:9|max_digits:10',
            'address'               => 'nullable|string',
            'location'              => 'nullable|string',
            'zip_code'              => 'nullable|numeric',
            'contact_name'          => 'nullable|string',
            'contact_title'         => 'nullable|string',
            'notes'                 => 'nullable|string'
        ]);

        if ($validator->fails()){
            return response()->json([
                'message' => 'Error validating data',
                'errors' => $validator->errors(),
                'status' => 400
            ], 400);
        }

        // Insert data
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
                'message' => 'Error creating supplier',
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
                'message' => 'Supplier not found',
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
        // Find the supplier by its ID
        $supplier = Supplier::find($id);

        // If the supplier does not exist, return an error
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found',
                'status' => 404
            ], 404);
        }

        // Get all products associated with this supplier
        $products = $supplier->products()->get();

        // Return the products associated with the supplier in JSON format
        return response()->json([
            'message' => 'Products associated with supplier retrieved successfully',
            'products' => $products,
            'status' => 200
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {   
        // Find the supplier by its ID and update it with the new values
        $supplier = Supplier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'                  => 'string|max:255',  
            'cif'                   => 'string|max:9',
            'description'           => 'nullable|string', 
            'email'                 => 'nullable|email',
            'phone'                 => 'nullable|numeric|min_digits:9|max_digits:10',
            'address'               => 'nullable|string',
            'location'              => 'nullable|string',
            'zip_code'              => 'nullable|numeric',
            'contact_name'          => 'nullable|string',
            'contact_title'         => 'nullable|string',
            'notes'                 => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error validating data',
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

        // Return a response with the updated supplier
        return response()->json([
            'message' => 'Supplier updated',
            'supplier' => $supplier,
            'status' => 200
        ], 200);
    }

    public function updatePartial(Request $request, $id)
    {
        // Find the supplier by its ID
        $supplier = Supplier::find($id);

        // If the supplier does not exist, return an error
        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier not found',
                'status' => 404
            ], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name'                  => 'string|max:255',  
            'cif'                   => 'string|max:9',
            'description'           => 'string', 
            'email'                 => 'email',
            'phone'                 => 'nullable|numeric|min_digits:9|max_digits:10',
            'address'               => 'string',
            'location'              => 'string',
            'zip_code'              => 'numeric',
            'contact_name'          => 'string',
            'contact_title'         => 'string',
            'notes'                 => 'string'
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
        $supplier->fill($validator->validated());
        $supplier->save();

        // Return a response with the updated supplier
        return response()->json([
            'message' => 'Supplier updated',
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
                'message' => 'Supplier not found',
                'status' => 404
            ];
    
            return response()->json($data, 404);
        }

        $supplier->delete();

        return response()->json([
            'message' => 'Supplier deleted successfully',
            'status' => 200
        ], 200);

    }
}
