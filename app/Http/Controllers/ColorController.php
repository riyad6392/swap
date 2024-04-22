<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Http\Requests\Color\CreateColorRequest;
use App\Http\Requests\Color\UpdateColorRequest;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Color List.
     *
     * @OA\Get(
     *     path="/api/color",
     *     tags={"Color"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
     *
     *          @OA\Parameter(
     *           in="query",
     *           name="search",
     *           required=true,
     *
     *           @OA\Schema(type="string"),
     *           example="red"
     *       ),
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
    public function index(Request $request)
    {
        $colors = Color::query();

        if ($request->has('search')) {
            $colors->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->get('get_all')) {
            return response()->json(['success' => true, 'data' => $colors->get()]);
        }

        $colors = $colors->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $colors]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create a new Brand.
     *
     *
     * @OA\Post (path="/api/color",
     *     tags={"Color"},
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
     *      @OA\Parameter(
     *          in="query",
     *          name="color_code",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="#fff",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Color created successfully."}}),
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
    public function store(CreateColorRequest $coloLRequest)
    {
        $color = Color::create([
            'name' => $coloLRequest->name,
            'code' => $coloLRequest->code,
        ]);

        return response()->json(['success' => true, 'message'=>'Color created successfully', 'data' => $color]);
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
     * Update Color
     *
     * @OA\Put (
     *     path="/api/color/{id}",
     *     tags={"Color"},
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
     *
     *     @OA\Parameter(
     *          in="query",
     *          name="color_code",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="#fff",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Brand updated successfully."}}),
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
    public function update(UpdateColorRequest $coloLRequest, string $id)
    {
        $color = Color::find($id);

        $color->update([
            'name' => $coloLRequest->name,
            'code' => $coloLRequest->code,
        ]);

        return response()->json(['success' => true, 'message' => 'Color updated successfully', 'data' => $color]);
    }

    /**
     * Delete Color
     *
     * @OA\Delete (
     *     path="/api/color/{id}",
     *     tags={"Color"},
     *     security={{ "apiAuth": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Color deleted successfully."}}),
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
        $color = Color::find($id);

        if (!$color) {
            return response()->json(['success' => false, 'message' => 'Color not found']);
        }

        $color->delete();

        return response()->json(['success' => true, 'message' => 'Color deleted successfully']);
    }
}
