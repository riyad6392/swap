<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminInvitation;
use App\Mail\SwapInitiated;
use App\Mail\UserApprovel;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use App\Mail\RegistrationSuccess;
use Illuminate\Support\Facades\Mail;
use Exception;

class AdminController extends Controller
{
    //const PER_PAGE = 10;

    /**
     * Admin List.
     *
     * @OA\Get(
     *     path="/api/admin",
     *     tags={"Admin"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=false,
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *          @OA\Schema(type="boolean"),
     *          example="1"
     *      ),
     *
     *     @OA\Parameter(
     *           in="query",
     *           name="search",
     *           required=false,
     *           @OA\Schema(type="string"),
     *           example="John Doe"
     *       ),
     *
     *     @OA\Parameter(
     *           in="query",
     *           name="sort",
     *           required=false,
     *           @OA\Schema(type="string"),
     *           example="asc"
     *       ),
     *
     *     @OA\Parameter(
     *           in="query",
     *           name="role",
     *           required=false,
     *           @OA\Schema(type="string"),
     *           example="admin"
     *       ),
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="data", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
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


        if ($request->has('role')) {
            $admins = $admins->whereHas('roles', function ($query) use ($request) {
                $query->where('name', $request->role);
            });
        }

        if ($request->has('id')) {
            $admins = $admins->whereHas('roles', function ($query) use ($request) {
                $query->where('id', $request->id);
            });
        }


        $admins = $admins->with('roles');

        if ($request->has('get_all')) {
            return response()->json(['success' => true, 'admins' => $admins->get()], 200);
        }

        $admins = $admins->paginate($request->paginate ?? 10);

        return response()->json(['success' => true, 'data' => $admins], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        $role = Role::find($request->role_id);
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt('password'),
            'phone' => $request->phone ?? ''
        ]);
        $admin->assignRole($role->name);

        $data = [
            'received_user_name' => $request->name,
            'requested_admin_first_name' => auth()->user()->first_name,
            'requested_admin_last_name' => auth()->user()->last_name,
            'role' => $role->name,
            'password' => 'password',
            'email' => $request->email,
        ];

        Mail::to($request->email)->send(new AdminInvitation($data));

        return response()->json(['success' => true, 'message' => 'Admin created successfully', 'data' => $admin], 201);
    }


    public function syncPermissions(int $user_id, int $role_id): JsonResponse
    {
        $user = Admin::findOrFail($user_id);

        $role = Role::findOrFail($role_id);

        $user->assignRole($role->name);


        return response()->json(['success' => true, 'message' => 'Permissions synced successfully', 'data' => $role], 201);
    }

    public function listPermissions(int $role_id): JsonResponse
    {
        $permissions = Permission::get();
        $permissions = $permissions->groupBy('group', true)->toArray();
        $role = Role::findOrFail($role_id);
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $role->id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return response()->json(['success' => true, 'data' => $permissions, 'rolePermissions' => $rolePermissions], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::with('roles')->find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $admin], 200);
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
    public function update(UpdateAdminRequest $request, string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin not found'], 404);
        }

        if ($request->has('role_id')) {
            $role = Role::find($request->role_id);
            $admin->syncRoles([$role->name]);
        }

        $admin->update([
            'name' => $request->name,
            'email' => $request->email
        ]);


        return response()->json(['success' => true, 'message' => 'Admin updated successfully', 'data' => $admin], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['success' => false, 'message' => 'Admin not found'], 404);
        }

        $admin->removeRole($admin->roles->first());
        $admin->delete();

        return response()->json(['success' => true, 'message' => 'Admin deleted successfully'], 200);
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
        //dd($user);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        $user->update(['is_approved_by_admin' => true]);

        $data = [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ];

        Mail::to($data['email'])->send(new UserApprovel($data));

        return response()->json(['success' => true, 'message' => 'User approved by admin'], 200);
    }

    public function sendEmail(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'email' => 'required|email',
                'name' => 'required|string|max:255',
            ]);

            Mail::to($data['email'])->send(new RegistrationSuccess($data));

            return response()->json(['message' => 'Email sent successfully!'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    public  function authUserAssignRole(){
        $user = auth()->user();

        $roles = $user->roles->first();

        $roles->role_permissions =  DB::table('role_has_permissions')
           ->where('role_has_permissions.role_id', $roles->id)
           ->join('permissions','role_has_permissions.permission_id','=','permissions.id')
           ->select('role_has_permissions.permission_id', 'role_has_permissions.permission_id', 'permissions.name', 'permissions.name')
              ->get();

        return response()->json(['success' => true, 'message' => 'Role assigned successfully', 'data' => $roles], 201);
    }


}
