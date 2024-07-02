<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:brand.index,brand.create,brand.edit,brand.delete', ['only' => ['index']]);
        $this->middleware('permission:brand.create', ['only' => ['store']]);
        $this->middleware('permission:brand.edit', ['only' => ['update']]);
        $this->middleware('permission:brand.delete', ['only' => ['destroy']]);

    }

    const PER_PAGE = 10;

    /**
     * Brand List.
     *
     * @OA\Get(
     *     path="/api/brand",
     *     tags={"Brand"},
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
     *
     *     @OA\Parameter(
     *           in="query",
     *           name="search",
     *           required=false,
     *
     *           @OA\Schema(type="string"),
     *           example="T-shirt"
     *
     *       ),
     *
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
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $brands = Brand::query();

        if ($request->has('search')) {
            $brands->where('name', 'like', '%' . request('search') . '%');
        }

        if ($request->get('get_all')) {

            return response()->json(['success' => true, 'data' => $brands->get()]);
        }

        $categories = $brands->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Create a new Brand.
     *
     *
     * @OA\Post (path="/api/brand",
     *     tags={"Brand"},
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
    public function store(StoreBrandRequest $brandRequest)
    {
        $brand = Brand::create([
            'name' => $brandRequest->name,
            'description' => $brandRequest->description ?? '',
            'is_published' => $brandRequest->is_published,
        ]);

//        if ($brandRequest->has('logo')){
//            FileUploadService::uploadFile($brandRequest->logo, $brand);
//        }

        return response()->json(['success' => true, 'message' => 'Brand created successfully', 'data' => $brand]);
    }

    /**
     * Brand Show.
     *
     * @OA\Get(
     *     path="/api/brand/{id}",
     *     tags={"Brand"},
     *     security={{ "apiAuth": {} }},
     *
     *     @OA\MediaType(mediaType="multipart/form-data"),
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
    public function show(string $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Brand not found']);
        }
        return response()->json(['success' => true, 'data' => $brand]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update Brand
     *
     * @OA\Put (
     *     path="/api/brand/{id}",
     *     tags={"Brand"},
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
    public function update(UpdateBrandRequest $brandRequest, string $id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Brand not found']);
        }

//        if ($brandRequest->has('logo')){
//            FileUploadService::uploadFile($brandRequest->logo, $brand);
//        }

        $brand->update([
            'name' => $brandRequest->name,
            'description' => $brandRequest->description ?? '',
        ]);

        return response()->json(['success' => true, 'message' => 'Brand updated successfully', 'data' => $brand]);
    }

    /**
     * Delete Brand
     *
     * @OA\Delete (
     *     path="/api/brand/{id}",
     *     tags={"Brand"},
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
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['success' => false, 'message' => 'Brand not found']);
        }

        $isBrandUsedInProducts = Product::where('brand_id', $id)->exists();

        if ($isBrandUsedInProducts) {
            return response()->json(['success' => false, 'message' => 'Brand is in use in product and cannot be deleted'], 403);
        }

        $brand->delete();
        return response()->json(['success' => true, 'message' => 'Brand deleted successfully']);
    }
}
