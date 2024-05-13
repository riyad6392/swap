<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductVariation\StoreProductVariationRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\ProductVariation\UpdateProductVariationRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    const PER_PAGE = 10;

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
    public function index(Request $request)
    {
        $inventories = Product::query();

        if ($request->name) {
            $inventories = $inventories->where('name', 'like', '%' . $request->name . '%');
        }

        $inventories = $inventories->with(
            'productVariations.images',
            'image',
            'category',
            'brand',
            'productVariations.size',
            'productVariations.color'
        );

        if ($request->get('get_all')) {
            return response()->json(['success' => true, 'data' => ProductResource::collection($inventories->get())->resource]);
        }

        $inventories = $inventories->paginate($request->pagination ?? self::PER_PAGE);

        return response()->json(['success' => true, 'data' => ProductResource::collection($inventories)->resource], 200);
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
<<<<<<< HEAD
     *         name="product_images",
=======
     *         name="product_image",
>>>>>>> ab61a0d2e38fd3b445d55187ca7552af3970c337
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
     *         name="variations[0][size_id]",
     *         required=true,
     *         description="Size of the product variation at index 0",
     *         @OA\Schema(type="string", example="XL"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="variations[0][color_id]",
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
    public function store(StoreProductRequest $productRequest, StoreProductVariationRequest $productVariantRequest): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $product = Product::create($productRequest->only([
                'name',
                'category_id',
                'user_id',
                'description',
                'brand_id',
                'is_publish'
            ]));

            if ($productRequest->has('product_image')) {
                FileUploadService::uploadImage([$productRequest->product_image], $product, 'image');
            }

            $this->storeVariations($productVariantRequest, $product);

            DB::commit();

            return response()->json(['success' => true, 'data' => 'Inventory created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        try {
            $product->load(
                'productVariations.images',
                'image',
                'category',
                'brand',
                'productVariations.size',
                'productVariations.color');

            return response()->json(['success' => true, 'data' => $product], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to retrieve product'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $product->load(
            'productVariations.images',
            'image',
            'category',
            'brand',
            'productVariations.size',
            'productVariations.color'
        );

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
     *         name="variations[0][unit_price]",
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
     *         description="Discount type of the product variation at index 0",
     *         @OA\Schema(type="string", example="percentage"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="variations[0][discount_start_date]",
     *         description="Start date of the discount for the product variation at index 0",
     *         @OA\Schema(type="string", format="date", example="2024-03-15"),
     *     ),
     *     @OA\Parameter(
     *         in="query",
     *         name="variations[0][discount_end_date]",
     *         description="End date of the discount for the product variation at index 0",
     *         @OA\Schema(type="string", format="date", example="2024-03-20"),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="variations[0][varient_images][]",
     *          description="Images of the product variation at index 0",
     *          @OA\Schema(type="string", example="[1,3]"),
     *      ),
     *
     *      @OA\Parameter(
     *          in="query",
     *          name="deleted_product_image_ids[]",
     *          description="IDs of the images to be deleted",
     *          @OA\Schema(type="string", example="[1,3]"),
     *      ),
     *      @OA\Parameter(
     *           in="query",
     *           name="deleted_product_variation_image_ids[]",
     *           description="IDs of the images to be deleted",
     *           @OA\Schema(type="string", example="[1,3]"),
     *       ),
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

    public function update(UpdateProductRequest $updateProductRequest, UpdateProductVariationRequest $updateProductVariationRequest, $id): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        try {
            DB::beginTransaction();

            $product->update([
                'name' => $updateProductRequest->name,
                'category_id' => $updateProductRequest->category_id,
                'user_id' => $updateProductRequest->user_id,
                'description' => $updateProductRequest->description,
                'brand_id' => $updateProductRequest->brand_id,
                'is_publish' => $updateProductRequest->is_publish
            ]);

            if ($updateProductRequest->has('deleted_product_image_ids')) {
                FileUploadService::deleteImages($this->deleted_product_image_ids, $product, 'image'); //deleted_product_image_ids is an array of image ids
            }

            if ($updateProductRequest->has('product_images')) {
                FileUploadService::uploadImage($updateProductRequest->product_images, $product, 'image');
            }


            if ($updateProductVariationRequest->has('variations')) {
                $this->storeVariations($updateProductVariationRequest, $product);
            }

            DB::commit();

            return response()->json(['success' => true, 'data' => $product->load('productVariations.images')], 201);
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
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $productImgIds = $product->image()->select('id')->pluck('id')->toArray();
        $variationImgIds = $product->productVariations->pluck('images')->flatten()->pluck('id')->toArray();
        $imageIds = array_merge($productImgIds, $variationImgIds);

        if ($imageIds) {
            FileUploadService::deleteImages($imageIds, $product, 'image');
        }

//        $product->image()->delete();
        $product->productVariations()->delete();
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product and related data deleted successfully'], 200);
    }

    protected function storeVariations($request, Product $product): void
    {
        if ($request->has('deleted_product_variation_image_ids')) {
            FileUploadService::deleteImages($request->deleted_product_variation_image_ids, $product, 'productVariations.images'); //deleted_product_variation_image_ids is an array of image ids
        }
        foreach ($request->variations as $key => $variationData) {
            $variation = $product->productVariations()->create([
                'size_id' => $variationData['size_id'],
                'color_id' => $variationData['color_id'],
                'unit_price' => $variationData['unit_price'],
                'stock' => $variationData['stock'],
                'discount' => $variationData['discount'] ?? 0,
                'quantity' => $variationData['quantity'],
                'discount_type' => $variationData['discount_type'] ??  'percentage' ,
                'discount_start_date' => $variationData['discount_start_date'] ?? now(),
                'discount_end_date' => $variationData['discount_end_date'] ?? now(),
            ]);

            if ($request->has('variations.' . $key . '.variant_images')) {
                FileUploadService::uploadImage($variationData['variant_images'], $variation);
            }
        }
    }

    /**
     *Change status to a product.
     *
     * @OA\Post (
     *     path="/api/change-product-status/{id}",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to change the status",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Product status updated successfully")
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
    public function changeStatus($id): \Illuminate\Http\JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $product->update(['is_publish' => !$product->is_publish]);

        return response()->json(['success' => true, 'message' => 'Product status updated successfully'], 200);
    }

    /**
     * Delete Product variation to a product.
     *
     * @OA\Post (
     *     path="/api/delete-product-variation}",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *
     *     @OA\Parameter(
     *          name="product_variation_id",
     *          in="path",
     *          required=true,
     *          description="ID of the product variation",
     *          @OA\Schema(type="integer", format="int64")
     *      ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example="true"),
     *             @OA\Property(property="message", type="string", example="Product status updated successfully")
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
    public function destroyProductVariation(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variation_id' => 'required|exists:product_variations,id'
        ]);

        $product = Product::where('id', $request->product_id)->where('user_id', auth()->id())->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        $productVariation = ProductVariation::where('id', $request->product_variation_id)->where('product_id', $request->product_id)->first();

        if (!$productVariation) {
            return response()->json(['success' => false, 'message' => 'Product variation not found'], 404);
        }

        $variationImgIds = $product->productVariations->pluck('images')->flatten()->pluck('id')->toArray();

        if ($variationImgIds) {
            FileUploadService::deleteImages($variationImgIds, $product, 'image');
        }

        $productVariation->delete();

        return response()->json(['success' => true, 'message' => 'Product variation deleted successfully'], 200);
    }
}
