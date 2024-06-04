<?php

namespace App\Http\Controllers;
use App\Facades\StripePaymentFacade;
use App\Http\Requests\User\ListUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserForAdminRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Brand;
use App\Models\Conversation;
use App\Models\Product;
use App\Models\Swap;
use App\Models\SwapExchangeDetails;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

//    public function __construct()
//    {
//        $this->middleware(['auth:sanctum', 'permission:admin'])->only(['index', 'store', 'show', 'update', 'destroy']);
//    }


    public function __construct()
    {

//        $this->middleware(['auth:admin-api', 'permission:user.index'])->only('index');
//        $this->middleware(['auth:admin-api', 'permission:user.show'])->only('show');
//        $this->middleware(['auth:admin-api', 'permission:user.edit'])->only('update');
//        $this->middleware(['auth:admin-api', 'permission:user.delete'])->only('destroy');

        $this->middleware('permission:brand-list|brand-create|brand-edit|brand-delete', ['only' => ['index','store']]);
        $this->middleware('permission:brand-create', ['only' => ['create','store']]);
        $this->middleware('permission:brand-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:brand-delete', ['only' => ['destroy']]);
    }

    const PER_PAGE = 10;
    /**
     * User List.
     *
     * @OA\Get(
     *     path="/api/admin/user",
     *     tags={"Admin User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=false,
     *
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *          @OA\Parameter(
     *           in="query",
     *           name="sort",
     *           required=false,
     *
     *           @OA\Schema(type="string"),
     *           example="asc/desc"
     *       ),
     *          @OA\Parameter(
     *           in="query",
     *           name="search",
     *           required=false,
     *
     *           @OA\Schema(type="string"),
     *           example="Imtiaz Ur Rahman Khan"
     *       ),
     *
     *        @OA\Parameter(
     *            in="query",
     *            name="sort",
     *            required=false,
     *
     *            @OA\Schema(type="string"),
     *            example="asc,desc"
     *        ),
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean"),
     *          example="1"
     *
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="message", type="json", example="Unauthenticated"),
     *           )
     *       )
     * )
     */
    public function index(ListUserRequest $listUserRequest)
    {
        $users = User::query()->with('receivedRatings');

        if ($listUserRequest->has('search')) {

            $users->where('first_name', 'like', '%' . request('search') . '%')
                ->orWhere('last_name', 'like', '%' . request('search') . '%');
        }

        if ($listUserRequest->has('sort')) {

            $users->orderBy('first_name', $listUserRequest->sort);
        }

        if ($listUserRequest->get('get_all')) {

            return response()->json(['success' => true, 'data' => $users->get()]);
        }

        $users = $users->paginate($listUserRequest->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * New User created.
     *
     * @OA\Post(
     *     path="/api/user-list",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="Imtiaz Ur Rahman",
     *     ),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         example="asc,desc",
     *     ),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="pagination",
     *         required=true,
     *         @OA\Schema(type="number"),
     *         example="10"
     *     ),
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="get_all",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *         example="1"
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="json", example={}),
     *             @OA\Property(property="links", type="json", example={}),
     *             @OA\Property(property="meta", type="json", example={}),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Invalid user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *         )
     *     )
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $admin_id = auth()->user()->id;

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt('password'),
            'is_approved_by_admin' => $request->is_approved_by_admin ?? false,
            'phone' => $request->phone,
            'approved_by' => $admin_id,
        ]);

        return response()->json(['success' => true, 'user' => $user], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = User::with('receivedRatings')->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }


        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserForAdminRequest $request, string $id): JsonResponse
    {

        $user = User::findOrFail($id);

        $user->update([
            'first_name' => $request->first_name ?? $user->first_name,
            'last_name' => $request->last_name ?? $user->last_name,
            'email' => $request->email ?? $user->email,
            'password' => bcrypt('password'),
            'is_approved_by_admin' => $request->is_approved_by_admin ?? $user->is_approved_by_admin,
            'phone' => $request->phone ?? $user->phone,

        ]);

        return response()->json(['success' => true, 'message' => 'User updated successfully']);


    }

    /**
     * Admin Delete.
     *
     * @OA\delete(
     *     path="/api/admin/user/destroy/{id}",
     *     tags={"Admin User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="true"),
     *                @OA\Property(property="message", type="json", example="User Deleted successfully")
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="message", type="json", example="Unauthenticated"),
     *           )
     *       )
     * )
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }

    /**
     * User List.
     *
     * @OA\Get(
     *     path="/api/user-list",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *           in="query",
     *           name="search",
     *           required=false,
     *
     *           @OA\Schema(type="string"),
     *           example="Imtiaz Ur Rahman",
     *       ),
     *
     *     @OA\Parameter(
     *            in="query",
     *            name="sort",
     *            required=false,
     *
     *            @OA\Schema(type="string"),
     *            example="asc,desc",
     *        ),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=true,
     *
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean"),
     *          example="1"
     *
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */
    public function userList(ListUserRequest $listUserRequest)
    {
        $users = User::query()->with('image')->withCount('receivedRatings');

        if ($listUserRequest->has('search')) {

            $users->where('first_name', 'like', '%' . request('search') . '%')
                ->orWhere('last_name', 'like', '%' . request('search') . '%');
        }

        if ($listUserRequest->has('sort')) {

            $users->orderBy('created_at', $listUserRequest->sort);
        }

        if ($listUserRequest->get('get_all')) {

            return response()->json(['success' => true, 'data' => $users->get()]);
        }

        $users = $users->paginate($listUserRequest->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * User Inventory List.
     *
     * @OA\Get(
     *     path="/api/user-inventory/{id}",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=true,
     *
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean"),
     *          example="1"
     *
     *      ),
     *      @OA\Parameter(
     *            in="query",
     *            name="search",
     *            required=true,
     *
     *            @OA\Schema(type="string"),
     *            example="Product name"
     *        ),
     *      @OA\Parameter(
     *             in="query",
     *             name="sort",
     *             required=true,
     *
     *             @OA\Schema(type="string"),
     *             example="asc,desc"
     *         ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */
    public function userInventory(ListUserRequest $listUserRequest, $id)
    {
        $user = User::with('image')->withCount('receivedRatings')->find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $inventory = $user->inventories()->with(
            'category',
            'brand',
            'productVariations.size',
            'productVariations.color'
        );

        if ($listUserRequest->has('search')) {

            $inventory->where('name', 'like', '%' . request('search') . '%');
        }

        if ($listUserRequest->has('sort')) {

            $inventory->orderBy('created_at', $listUserRequest->sort);
        }

        $user = new UserResource($user);

        if ($listUserRequest->get('get_all')) {

            return response()->json(['success' => true,
                'data' => [
                    'user' => $user,
                    'inventory' => ProductResource::collection($inventory->get())->resource
                ]
            ]);
        }

        $inventory = $inventory->paginate($listUserRequest->pagination ?? self::PER_PAGE);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'inventory' => ProductResource::collection($inventory)->resource
            ]
        ]);
    }

    /**
     * User store List.
     *
     * @OA\Get(
     *     path="/api/user-store/{id}",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="pagination",
     *          required=true,
     *
     *          @OA\Schema(type="number"),
     *          example="10"
     *      ),
     *     @OA\Parameter(
     *           in="query",
     *           name="search",
     *           required=true,
     *
     *           @OA\Schema(type="string"),
     *           example="product name"
     *       ),
     *     @OA\Parameter(
     *            in="query",
     *            name="sort",
     *            required=true,
     *
     *            @OA\Schema(type="string"),
     *            example="asc,desc"
     *        ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean"),
     *          example="1"
     *
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */
    public function userStore(Request $request, $id)
    {

        $user = User::with('image')->withCount('receivedRatings')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $inventory = $user->store()
            ->with(
                'image',
                'category',
                'brand',
                'productVariations.size',
                'productVariations.color'
            );

        if ($request->search) {
            $inventory->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('sort')) {

            $inventory->orderBy('created_at', $request->sort);
        }

        $user = new UserResource($user);

        if ($request->get('get_all')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'store' => ProductResource::collection($user->store()->get())->resource]
            ]);
        }

        $inventory = $inventory->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'store' => ProductResource::collection($inventory)->resource
            ]
        ]);
    }

    /**
     * User Profile.
     *
     * @OA\Get(
     *     path="/api/user-profile",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */

    public function userProfile()
    {
        $user = User::with('image', 'activeSubscriptions', 'paymentMethods', 'billings.swap')->find(auth()->id());
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * User update profile.
     *
     * @OA\Post(
     *     path="/api/update-profile/{id}",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="first_name",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="Imtiaz"
     *      ),
     *
     *       @OA\Parameter(
     *           in="query",
     *           name="last_name",
     *           required=true,
     *
     *           @OA\Schema(type="string"),
     *           example="Khan"
     *       ),
     *
     *       @OA\Parameter(
     *            in="query",
     *            name="phone",
     *            required=true,
     *
     *            @OA\Schema(type="string"),
     *            example="Khan"
     *        ),
     *
     *       @OA\Parameter(
     *             in="query",
     *             name="image",
     *             required=true,
     *
     *             @OA\Schema(type="file"),
     *             example="file"
     *         ),
     *
     *            @OA\Parameter(
     *              in="query",
     *              name="resale_license",
     *              required=true,
     *
     *              @OA\Schema(type="file"),
     *              example="file"
     *          ),
     *
     *         @OA\Parameter(
     *               in="query",
     *               name="photo_of_id",
     *               required=true,
     *
     *               @OA\Schema(type="file"),
     *               example="file"
     *           ),
     *              @OA\Parameter(
     *                in="query",
     *                name="photo_of_id",
     *                required=true,
     *
     *                @OA\Schema(type="file"),
     *                example="file"
     *            ),
     *          @OA\Parameter(
     *                 in="query",
     *                 name="business_name",
     *                 required=true,
     *
     *                 @OA\Schema(type="string"),
     *                 example="Business name"
     *             ),
     *          @OA\Parameter(
     *                  in="query",
     *                  name="business_address",
     *                  required=true,
     *
     *                  @OA\Schema(type="string"),
     *                  example="Business address"
     *              ),
     *          @OA\Parameter(
     *                  in="query",
     *                  name="online_store_url",
     *                  required=true,
     *
     *                  @OA\Schema(type="string"),
     *                  example="http://127.0.0.1:8000/api/documentation#/User/ad5b4db3132c00564bd7eede30c3e23a"
     *              ),
     *
     *          @OA\Parameter(
     *                   in="query",
     *                   name="ein",
     *                   required=true,
     *
     *                   @OA\Schema(type="string"),
     *                   example="ein"
     *               ),
     *          @OA\Parameter(
     *                   in="query",
     *                   name="about_me",
     *                   required=true,
     *
     *                   @OA\Schema(type="string"),
     *                   example="this is a description about me"
     *               ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="get_all",
     *          required=false,
     *
     *          @OA\Schema(type="boolean"),
     *          example="1"
     *
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */

    public function updateProfile(UpdateUserRequest $userRequest)
    {
        try {
            DB::beginTransaction();

            $user = User::find(auth()->id());

            $resaleLicense = null;
            $photoOfId = null;

            if ($userRequest->has('image')) {
                if ($user->image) {
                    FileUploadService::deleteImages([$user->image->id], $user, 'image');
                }
                FileUploadService::uploadImage($userRequest->image, $user, 'image');
            }


            if ($userRequest->has('resale_license')) {
                if ($user->resale_license && Storage::fileExists($user->resale_license)) Storage::delete($user->resale_license);
                $resaleLicense = FileUploadService::uploadFile(
                    $userRequest->resale_license,
                    $user,
                    'resale_license'
                );
            }


            if ($userRequest->has('photo_of_id')) {
                if ($user->photo_of_id) Storage::delete($user->photo_of_id);
                $photoOfId = FileUploadService::uploadFile($userRequest->photo_of_id, $user, 'photo_of_id');
            }

            $user->update([
                'first_name' => $userRequest->first_name,
                'last_name' => $userRequest->last_name,
                'phone' => $userRequest->phone,
                'business_name' => $userRequest->business_name,
                'business_address' => $userRequest->business_address,
                'resale_license' => $resaleLicense,
                'photo_of_id' => $photoOfId,
                'online_store_url' => $userRequest->online_store_url,
                'ein' => $userRequest->ein,
                'about_me' => $userRequest->about_me,
            ]);

            $user = $user->load('image');

            DB::commit();
            return response()->json(['success' => true, 'data' => $user]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 500);
        }
    }



    /**
     * Admin Approve.
     *
     * @OA\Get(
     *     path="/api/admin/approved-user/{id}",
     *     tags={"Admin User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="true"),
     *                @OA\Property(property="message", type="json", example="User Update successfully")
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="message", type="json", example="Unauthenticated"),
     *           )
     *       )
     * )
     */
    public function approvedUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->is_approved_by_admin = !$user->is_approved_by_admin;
            $user->save();
            return response()->json(['success' => true, 'message' => 'User updated successfully', 'data' => $user]);
        }
        return response()->json(['success' => false, 'message' => 'User not found'], 404);
    }



    /**
     * User Dashboard.
     *
     * @OA\Get(
     *     path="/api/user-dashboard",
     *     tags={"User"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="data", type="json", example={}),
     *               @OA\Property(property="links", type="json", example={}),
     *               @OA\Property(property="meta", type="json", example={}),
     *           )
     *       ),
     *
     *       @OA\Response(
     *           response=401,
     *           description="Invalid user",
     *
     *           @OA\JsonContent(
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */


    public function userDashboard()
    {
        $user = User::find(auth()->id());

        if ($user) {
            $swap_count = Swap::where('user_id', $user->id)->count();
            $product_count = Product::where('user_id', $user->id)->count();
            $swap_exchange_amount = SwapExchangeDetails::where('user_id', $user->id)->sum('amount');
            $swapExchangeDetails = SwapExchangeDetails::where('user_id', $user->id)->get();
            $conversation_list = Conversation::where('user_id', $user->id)->get();

            return response()->json([
                'success' => true,
                'message' => 'User data retrieved successfully',
                'data' => [
                    'user' => $user,
                    'swap_count' => $swap_count,
                    'product_count' => $product_count,
                    'swap_exchange_amount' => $swap_exchange_amount,
                    'swap_exchange_details' => $swapExchangeDetails,
                    'conversation_list' => $conversation_list
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ]);
    }



}
