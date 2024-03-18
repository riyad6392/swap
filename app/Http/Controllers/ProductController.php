<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariation\StoreProductVariationRequest;
use App\Models\Image;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\FileUploadService;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    const UPDATE_REQUEST_TYPE = ['put', 'patch'];
    private $deleted_image_ids = [];
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
        $inventories = Product::with('productVariations.images', 'images')->paginate(10);
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "user_id", "images", "variations"},
     *             @OA\Property(property="name", type="string", example="Product Name"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Product Description"),
     *             @OA\Property(property="deleted_image_ids", type="array", example={"1", "2", "3"}, @OA\Items(type="integer")),
     *             @OA\Property(
     *                 property="images",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"path"},
     *                     @OA\Property(property="path", type="string", example="image1.jpg")
     *                 ),
     *                 example={{"path": "image1.jpg"}, {"path": "image2.jpg"}}
     *             ),
     *             @OA\Property(
     *                 property="variations",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"price", "stock", "quantity"},
     *                     @OA\Property(property="size", type="string", example="XL"),
     *                     @OA\Property(property="color", type="string", example="Red"),
     *                     @OA\Property(property="price", type="number", format="decimal", example=19.99),
     *                     @OA\Property(property="stock", type="integer", example=100),
     *                     @OA\Property(property="discount", type="number", format="double", example=10.5),
     *                     @OA\Property(property="quantity", type="integer", example=50),
     *                     @OA\Property(property="discount_type", type="string", example="percentage"),
     *                     @OA\Property(property="discount_start_date", type="string", format="date", example="2024-03-15"),
     *                     @OA\Property(property="discount_end_date", type="string", format="date", example="2024-03-20"),
     *                     @OA\Property(
     *                         property="varient_images",
     *                         type="array",
     *                         @OA\Items(
     *                             required={"path"},
     *                             @OA\Property(property="path", type="string", example="image1.jpg")
     *                         ),
     *                         example={{"path": "image1.jpg"}, {"path": "image2.jpg"}}
     *                     )
     *                 ),
     *                 example={
     *                     {"size": "XL", "color": "Red", "price": 19.99, "stock": 100, "discount": 10.5, "quantity": 50, "discount_type": "percentage", "discount_start_date": "2024-03-15", "discount_end_date": "2024-03-20", "varient_images": {{"path": "image1.jpg"}, {"path": "image2.jpg"}}},
     *                     {"size": "L", "color": "Blue", "price": 24.99, "stock": 80, "discount": null, "quantity": 30, "discount_type": null, "discount_start_date": null, "discount_end_date": null, "varient_images": {{"path": "image3.jpg"}, {"path": "image4.jpg"}}}
     *                 }
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Product Created successfully."}}),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Some this is wrong"}}),
     *         )
     *     )
     * )
     */
    public function store(StoreProductRequest $productRequest , StoreProductVariationRequest $productVariantRequest)
    {
        $this->deleted_image_ids = $productRequest->has('deleted_image_ids') ? json_decode($productRequest->deleted_image_ids) : [];
        try {
            DB::beginTransaction();

            $product = Product::create($productRequest->only([
                'name',
                'category_id',
                'user_id',
                'description',
            ]));

            if ($productRequest->has('images')) {
                FileUploadService::uploadFile($productRequest->images, $product, $this->deleted_image_ids);
            }
            $this->storeVariations($productVariantRequest, $product);
            DB::commit();

            return response()->json(['success' => true, 'data' => $product], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeVariations($request, Product $product)
    {
        foreach ($request->variations as $variationData) {
            $variation = $product->productVariations()
                ->updateOrCreate(
                    ['product_id' => $product->id],
                    $variationData
                );
            if (isset($variationData['varient_images']) && count($variationData['varient_images'])) {
                FileUploadService::uploadFile($variationData['varient_images'], $variation, $this->deleted_image_ids);
            }
        }
    }

    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(Product $product)
    {
        $product->load('images', 'productVariations');

        return response()->json(['success' => true, 'data' => $product], 200);
    }

    /**
     * Update the product.
     *
     * @OA\Put (
     *     path="/api/product/{id}",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to be updated",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "category_id", "user_id", "images", "variations"},
     *             @OA\Property(property="name", type="string", example="Updated Product Name"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="Updated Product Description"),
     *             @OA\Property(property="deleted_image_ids", type="array", example={"1", "2", "3"}, @OA\Items(type="integer")),
     *             @OA\Property(
     *                 property="images",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"path"},
     *                     @OA\Property(property="path", type="string", example="image3.jpg")
     *                 ),
     *                 example={{"path": "image3.jpg"}, {"path": "image4.jpg"}}
     *             ),
     *             @OA\Property(
     *                 property="variations",
     *                 type="array",
     *                 @OA\Items(
     *                     required={"price", "stock", "quantity"},
     *                     @OA\Property(property="size", type="string", example="M"),
     *                     @OA\Property(property="color", type="string", example="Green"),
     *                     @OA\Property(property="price", type="number", format="decimal", example=29.99),
     *                     @OA\Property(property="stock", type="integer", example=150),
     *                     @OA\Property(property="discount", type="number", format="double", example=15.75),
     *                     @OA\Property(property="quantity", type="integer", example=80),
     *                     @OA\Property(property="discount_type", type="string", example="fixed"),
     *                     @OA\Property(property="discount_start_date", type="string", format="date", example="2024-03-18"),
     *                     @OA\Property(property="discount_end_date", type="string", format="date", example="2024-03-25"),
     *                     @OA\Property(property="varient_images", type="array", @OA\Items(type="string", example="image1.jpg"), nullable=true),
     *                 ),
     *                 example={
     *                     {"size": "M", "color": "Green", "price": 29.99, "stock": 150, "discount": 15.75, "quantity": 80, "discount_type": "fixed", "discount_start_date": "2024-03-18", "discount_end_date": "2024-03-25", "varient_images": {"image1.jpg", "image2.jpg"}},
     *                     {"size": "S", "color": "Yellow", "price": 34.99, "stock": 120, "discount": null, "quantity": 60, "discount_type": null, "discount_start_date": null, "discount_end_date": null, "varient_images": null}
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="data", type="json", example={"id": 1, "name": "Updated Product Name", "category_id": 1, "user_id": 1, "description": "Updated Product Description"}),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid user",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="errors", type="json", example={"message": {"Some this is wrong"}}),
     *         )
     *     )
     * )
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'category_id' => 'integer',
            'user_id' => 'integer',
            'description' => 'nullable|string',
            'images' => 'array',
            'images.*.path' => 'string',
            'deleted_image_ids' => 'nullable',
            'variations' => 'array',
            'variations.*.size' => 'nullable|string',
            'variations.*.color' => 'nullable|string',
            'variations.*.price' => 'numeric',
            'variations.*.stock' => 'integer',
            'variations.*.discount' => 'nullable|numeric',
            'variations.*.quantity' => 'integer',
            'variations.*.discount_type' => 'nullable|string',
            'variations.*.discount_start_date' => 'nullable|date',
            'variations.*.discount_end_date' => 'nullable|date',
            'variations.*.varient_images' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $this->deleted_image_ids = $request->has('deleted_image_ids') ? json_decode($request->deleted_image_ids) : [];

        try {
            DB::beginTransaction();

            $product->update($request->only(['name', 'category_id', 'user_id', 'description']));

            if ($request->has('images')) {
                FileUploadService::uploadFile($request->images, $product, $this->deleted_image_ids);
            }

            $this->storeVariations($request, $product);
            DB::commit();

            return response()->json(['success' => true, 'data' => $product], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update product'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete (
     *     path="/api/product/{id}",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to be deleted",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Product and related data deleted successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         ),
     *     )
     * )
     */
    public function destroy(Product $product)
    {
        $product->images()->delete();
        $product->productVariations()->delete();
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product and related data deleted successfully'], 200);
    }
}
