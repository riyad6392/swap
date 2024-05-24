<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolePermission\StoreRoleRequest;
use App\Http\Requests\RolePermission\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::query();

        if ($request->has('search')) {
            $roles = $roles->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort')) {
            $roles = $roles->orderBy('name', $request->sort);
        }

        if($request->has('get_all')){
            return response()->json(['success'=> true,'roles' => $roles->get()], 200);
        }

        $roles = $roles->paginate( $request->paginate ?? 10);

        return response()->json(['success'=> true,'roles' => $roles], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $role = Role::create(['name' => $request->name]);

        return response()->json(['success'=> true,'message' => 'Role created successfully', 'role' => $role], 201);
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
    public function update(UpdateRoleRequest $request, string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['success'=> false,'message' => 'Role not found'], 404);
        }

        $role->update([
           'name' => $request->name ?? ''
        ]);
        return response()->json(['success'=> true,'message' => 'Role updated successfully', 'role' => $role], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['success'=> false,'message' => 'Role not found'], 404);
        }

        $role->revokePermissionTo($role->permissions);
        $role->delete();
        return response()->json(['success'=> true,'message' => 'Role deleted successfully'], 200);
    }
}
