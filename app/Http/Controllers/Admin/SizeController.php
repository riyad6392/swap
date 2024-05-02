<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Size\CreateSizeRequest;
use App\Http\Requests\Size\UpdateSizeRequest;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Size List.
     *
     * @OA\Get(
     *     path="/api/size",
     *     tags={"Size"},
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
     *     @OA\Parameter(
     *           in="query",
     *           name="description",
     *           required=false,
     *
     *           @OA\Schema(type="string"),
     *           example="This is just example description."
     *       ),
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
    public function index(Request $request)
    {
        $size = Size::query();

        if ($request->has('search')) {
            $size->where('name', 'like', '%' . request('search') . '%');
        }

        if ($request->get('get_all')) {

            return response()->json(['success' => true, 'data' => $size->get()]);
        }

        $categories = $size->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create a new Color.
     *
     *
     * @OA\Post (path="/api/size",
     *     tags={"Size"},
     *     security={{ "apiAuth": {} }},
     *
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="name",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="Doel Rana",
     *     ),
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="description",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="This is just example description.",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Brand created successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */
    public function store(CreateSizeRequest $sizeRequest)
    {
        $size = Size::create([
            'name' => $sizeRequest->name,
            'description' => $sizeRequest->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Size created successfully', 'data' => $size]);
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
     * Update Size
     *
     * @OA\Put (
     *     path="/api/size/{id}",
     *     tags={"Size"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\Parameter(
     *         in="query",
     *         name="name",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="Doel Rana",
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="description",
     *          required=false,
     *
     *          @OA\Schema(type="string"),
     *          example="This is just example description.",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Size updated successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */
    public function update(UpdateSizeRequest $sizeRequest, string $id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['success' => false, 'message' => 'Size not found']);
        }

        $size->update([
            'name' => $sizeRequest->name,
            'description' => $sizeRequest->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Size updated successfully', 'data' => $size]);
    }

    /**
     * Delete Size
     *
     * @OA\Delete (
     *     path="/api/size/{id}",
     *     tags={"Size"},
     *     security={{ "apiAuth": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Brand deleted successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid data",
     *
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"The given data was invalid."}}),
     *          )
     *      )
     * )
     */
    public function destroy(string $id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['success' => false, 'message' => 'Size not found']);
        }

        $size->delete();

        return response()->json(['success' => true, 'message' => 'Size deleted successfully']);
    }
}
