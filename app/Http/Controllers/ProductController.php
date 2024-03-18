<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Product List.
     *
     * @OA\Get(
     *     path="/api/inventory",
     *     tags={"Inventory"},
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
     *          @OA\Schema(type="boolean")
     *      ),
     *     @OA\Response(
     *           response=200,
     *           description="success",
     *
     *           @OA\JsonContent(
     *
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
     *
     *               @OA\Property(property="success", type="boolean", example="false"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Unauthenticated"}}),
     *           )
     *       )
     * )
     */
    public function index()
    {
        $inventories = Product::with('productVariations')->paginate(10);
        return response()->json(['success' => true, 'data' => $inventories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Product Created.
     *
     * @OA\Post (
     *     path="/api/product",
     *     tags={"Inventory"},
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
     *         in="query",
     *         name="category_id",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="1",
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="user_id",
     *         required=true,
     *
     *         @OA\Schema(type="string"),
     *         example="1",
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="description",
     *          required=true,
     *
     *          @OA\Schema(type="string"),
     *          example="This is just an address",
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Product Created successfully."}}),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Invalid user",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="false"),
     *              @OA\Property(property="errors", type="json", example={"message": {"Some this is wrong"}}),
     *          )
     *      )
     * )
     */
    public function store(StoreProductRequest $request)
    {
        return response()->json($request);

        $validateData = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        if ($validateData->fails()) {
            return response()->json(['success' => false, 'errors' => $validateData->errors()], 422);
        }else {

//            return response()->json($request);
            if($request->has('files')){
                Product::create([
                    'name' => $request->name,
                    'category_id' => $request->category_id,
                    'user_id' => $request->user_id,
                    'description' => $request->description ?? '',
                ]);
            }
//            ProductVariation::
            return response()->json(['success' => true, 'message' => 'Product created successfully']);
        }
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
        //
    }
}
