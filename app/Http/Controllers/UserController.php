<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ListUserRequest;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    const PER_PAGE = 10;

    /**
     * User List.
     *
     * @OA\Get(
     *     path="/api/user",
     *     tags={"Admin User List"},
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
    public function index(ListUserRequest $listUserRequest)
    {
        $users = User::query()->with('receivedRatings');

        if ($listUserRequest->has('search')) {

            $users->where('name', 'like', '%' . request('search') . '%');
        }

        if ($listUserRequest->has('sort')){

            $users->orderBy('name', $listUserRequest->sort);
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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

        if ($request->get('get_all')) {
            return response()->json(['success' => true, 'data' => $user->products()->get()]);
        }

        $inventory = $user->products()->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $inventory]);
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
        //
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
    public function userList(ListUserRequest $listUserRequest){

        $users = User::query()->with('image');

        if ($listUserRequest->has('search')) {

            $users->where('name', 'like', '%' . request('search') . '%');
        }

        if ($listUserRequest->has('sort')){

            $users->orderBy('name', $listUserRequest->sort);
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
    public function userInventory(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if ($request->get('get_all')) {
            return response()->json(['success' => true, 'data' => $user->products()->get()]);
        }

        $inventory = $user->products()->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $inventory]);
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(auth()->id());

        if ($request->has('image')) {
            FileUploadService::uploadFile($request->image, $user, 'image');
        }

        $user->update($request->all());

        return response()->json(['success' => true, 'data' => $user]);
    }
}
