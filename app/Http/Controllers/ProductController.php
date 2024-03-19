<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariation\StoreProductVariationRequest;
use App\Models\Image;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\ProductVariation\UpdateProductVariationRequest;
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
 * @OA\Post(
 *     path="/api/product",
 *     tags={"Inventory"},
 *     summary="Create a new product with variations",
 *     @OA\Parameter(
 *         in="query",
 *         name="name",
 *         required=true,
 *         description="Name of the product",
 *         @OA\Schema(type="string", example="Product Name"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="category_id",
 *         required=true,
 *         description="ID of the category",
 *         @OA\Schema(type="integer", example=1),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="user_id",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer", example=1),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="description",
 *         required=true,
 *         description="Description of the product",
 *         @OA\Schema(type="string", example="Product Description"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="product_images[]",
 *         required=true,
 *         description="Images of the product",
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 required={"path"},
 *                 @OA\Property(property="path", type="string", example="updated_product_image1.jpg"),
 *             ),
 *         ),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][size]",
 *         required=true,
 *         description="Size of the product variation at index 0",
 *         @OA\Schema(type="string", example="XL"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][color]",
 *         required=true,
 *         description="Color of the product variation at index 0",
 *         @OA\Schema(type="string", example="Red"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][price]",
 *         required=true,
 *         description="Price of the product variation at index 0",
 *         @OA\Schema(type="number", format="decimal", example=19.99),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][stock]",
 *         required=true,
 *         description="Stock of the product variation at index 0",
 *         @OA\Schema(type="integer", example=100),
 *     ),
 *    @OA\Parameter(
 *         in="query",
 *         name="variations[0][discount]",
 *         required=true,
 *         description="Discount of the product variation at index 0",
 *         @OA\Schema(type="number", format="double", example=10.5),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][quantity]",
 *         required=true,
 *         description="Quantity of the product variation at index 0",
 *         @OA\Schema(type="integer", example=50),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][discount_type]",
 *         required=true,
 *         description="Discount type of the product variation at index 0",
 *         @OA\Schema(type="string", example="percentage"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][discount_start_date]",
 *         required=true,
 *         description="Start date of the discount for the product variation at index 0",
 *         @OA\Schema(type="string", format="date", example="2024-03-15"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][discount_end_date]",
 *         required=true,
 *         description="End date of the discount for the product variation at index 0",
 *         @OA\Schema(type="string", format="date", example="2024-03-20"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][varient_images]",
 *         required=true,
 *         description="Images of the product variation at index 0",
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 required={"path"},
 *                 @OA\Property(property="path", type="string", example="image1.jpg"),
 *             ),
 *             example={{"path": "image1.jpg"}, {"path": "image2.jpg"}},
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="errors", type="json", example={"message": "Product Created successfully."}),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid user",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="errors", type="json", example={"message": "Some this is wrong"}),
 *         ),
 *     ),
 * )
 */
    public function store(StoreProductRequest $productRequest , StoreProductVariationRequest $productVariantRequest)
    {
        try {
            DB::beginTransaction();

            $product = Product::create($productRequest->only([
                'name',
                'category_id',
                'user_id',
                'description',
            ]));
            if ($productRequest->has('product_images')) {
                FileUploadService::uploadFile($productRequest->product_images, $product);
            }
            $this->storeVariations($productVariantRequest, $product);
            DB::commit();

            return response()->json(['success' => true, 'data' => $product], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeVariations($request, Product $product, array $deleted_image_ids = [])
    {
        foreach ($request->variations as $variationData) {
            $variation = $product->productVariations()
                ->updateOrCreate(
                    ['product_id' => $product->id],
                    $variationData
                );
            if (isset($variationData['varient_images']) && count($variationData['varient_images'])) {
                FileUploadService::uploadFile($variationData['varient_images'], $variation, $deleted_image_ids);
            } else {
                FileUploadService::deleteImages($this->deleted_image_ids, $product);
            }
        }
    }

    /**
     * Retrieve a specific product.
     *
     * @OA\Get(
     *     path="/api/product/{id}",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="get singe product by product id",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *    @OA\Response(
    *         response=200,
    *         description="success",
    *         @OA\JsonContent(
    *             @OA\Property(property="success", type="boolean", example="true"),
    *             @OA\Property(property="errors", type="json", example={"message": {"Product Created successfully."}}),
    *         ),
    *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="false"),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */

    public function show(Product $product)
    {

        try {
            $product->load('images', 'productVariations');

            return response()->json(['success' => true, 'data' => $product], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve product'], 500);
        }
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
 * Update Product.
 *
 * @OA\Put(
 *     path="/api/product/{id}",
 *     tags={"Inventory"},
 *     summary="Update an existing product with variations",
 *     @OA\Parameter(
 *         in="path",
 *         name="id",
 *         required=true,
 *         description="ID of the product to update",
 *         @OA\Schema(type="integer", example=1),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="name",
 *         required=true,
 *         description="Name of the product",
 *         @OA\Schema(type="string", example="Updated Product Name"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="category_id",
 *         required=true,
 *         description="ID of the category",
 *         @OA\Schema(type="integer", example=2),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="user_id",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer", example=2),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="description",
 *         required=true,
 *         description="Description of the product",
 *         @OA\Schema(type="string", example="Updated Product Description"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][size]",
 *         required=true,
 *         description="Size of the product variation at index 0",
 *         @OA\Schema(type="string", example="M"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][color]",
 *         required=true,
 *         description="Color of the product variation at index 0",
 *         @OA\Schema(type="string", example="Blue"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][price]",
 *         required=true,
 *         description="Price of the product variation at index 0",
 *         @OA\Schema(type="number", format="decimal", example=24.99),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][stock]",
 *         required=true,
 *         description="Stock of the product variation at index 0",
 *         @OA\Schema(type="integer", example=80),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][discount]",
 *         required=true,
 *         description="Discount of the product variation at index 0",
 *         @OA\Schema(type="number", format="double", example=10.5),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][quantity]",
 *         required=true,
 *         description="Quantity of the product variation at index 0",
 *         @OA\Schema(type="integer", example=30),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][discount_type]",
 *         required=true,
 *         description="Discount type of the product variation at index 0",
 *         @OA\Schema(type="string", example="percentage"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][discount_start_date]",
 *         required=true,
 *         description="Start date of the discount for the product variation at index 0",
 *         @OA\Schema(type="string", format="date", example="2024-03-15"),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][discount_end_date]",
 *         required=true,
 *         description="End date of the discount for the product variation at index 0",
 *         @OA\Schema(type="string", format="date", example="2024-03-20"),
 *     ),
 *      @OA\Parameter(
 *         in="query",
 *         name="deleted_image_ids[]",
 *         required=true,
 *         description="IDs of the images to be deleted",
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 type="integer",
 *                 example={1, 5, 6, 7},
 *             ),
 *         ),
 *     ),
 *     @OA\Parameter(
 *         in="query",
 *         name="variations[0][varient_images][]",
 *         required=true,
 *         description="Images of the product variation at index 0",
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 required={"path"},
 *                 @OA\Property(property="path", type="string", example="updated_image1.jpg"),
 *             ),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="errors", type="json", example={"message": "Product Updated successfully."}),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Invalid data",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="errors", type="json", example={"message": "Some data is invalid"}),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="errors", type="json", example={"message": "Product not found"}),
 *         ),
 *     ),
 * )
 */

    public function update(UpdateProductRequest $updateProductRequest, UpdateProductVariationRequest $updateProductVariationRequest, Product $product)
    {
        $this->deleted_image_ids = $updateProductRequest->has('deleted_image_ids') ? $updateProductRequest->deleted_image_ids : [];
        try {
            DB::beginTransaction();
            $product->update($updateProductRequest->only(['name', 'category_id', 'user_id', 'description']));

            if ($updateProductRequest->has('product_images')) {
                FileUploadService::uploadFile($updateProductRequest->product_images, $product, $this->deleted_image_ids);
            } else {
                FileUploadService::deleteImages($this->deleted_image_ids, $product);
            }

            $this->storeVariations($updateProductVariationRequest, $product, $this->deleted_image_ids);
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
