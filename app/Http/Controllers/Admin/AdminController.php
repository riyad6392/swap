<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $admins = Admin::query();

        if ($request->has('search')) {
            $admins = $admins->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort')) {
            $admins = $admins->orderBy('name', $request->sort);
        }

        if($request->has('get_all')){
            return response()->json(['success'=> true,'admins' => $admins->get()], 200);
        }

        $admins = $admins->paginate( $request->paginate ?? 10);

        return response()->json(['success'=> true,'data' => $admins], 200);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin not found'], 404);
        }

        return response()->json(['success'=> true,'data' => $admin], 200);
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
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['success'=> false,'message' => 'Admin not found'], 404);
        }

        $admin->update($request->all());

        return response()->json(['success'=> true,'message' => 'Admin updated successfully', 'data' => $admin], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * User Approved by Admin.
     * @OA\Post (
     *     path="/api/admin/approve-user/{user}",
     *     tags={"Admin Authentication"},
     *     security={{ "apiAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="User approved by admin!"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid user",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *          )
     *      )
     * )
     */
    public function approveUser(User $user)
    {
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        $user->update(['is_approved_by_admin' => true]);
        return response()->json(['success' => true, 'message' => 'User approved by admin'], 200);
    }
}
