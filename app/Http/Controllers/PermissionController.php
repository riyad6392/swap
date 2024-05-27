<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolePermission\StorePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $permissions = Permission::query();

        if ($request->has('search')) {
            $permissions = $permissions->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort')) {
            $permissions = $permissions->orderBy('name', $request->sort);
        }

        if($request->has('get_all')){
            return response()->json(['success'=> true,'permissions' => $permissions->get()], 200);
        }

        $permissions = $permissions->paginate( $request->paginate ?? 10);

        return response()->json(['success'=> true,'permissions' => $permissions], 200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create(['name' => $request->name]);
        return response()->json(['success'=> true,'message' => 'Permission created successfully', 'permission' => $permission], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['success'=> false,'message' => 'Permission not found'], 404);
        }

        $permission->delete();
        return response()->json(['success'=> true,'message' => 'Permission deleted successfully'], 200);
    }
}
