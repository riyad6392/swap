<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    const PER_PAGE = 10;

    /**
     * Category List.
     *
     * @OA\Get(
     *     path="/api/category",
     *     tags={"Category"},
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
    public function index(Request $request)
    {
        $categories = Category::query();

        if ($request->get('get_all')) {

            return response()->json(['success' => true, 'data' => $categories->get()]);
        }

        $categories = $categories->paginate($request->pagination ?? self::PER_PAGE);

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
     * Create a new Category.
     *
     *
     * @OA\Post (path="/api/category",
     *     tags={"Category"},
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
     *               @OA\Property(property="errors", type="json", example={"message": {"Category created successfully."}}),
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
    public function store(StoreCategoryRequest $request)
    {
        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_published'=>$request->is_published,
        ]);
        return response()->json(['success' => true, 'message' => 'Category created successfully.']);
    }

    /**
     * Category Show.
     *
     * @OA\Get(
     *     path="/api/category/{id}",
     *     tags={"Category"},
     *     security={{ "apiAuth": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="json", example={"id": 1,"name": "Category 1","description": "Description", "created_at": "2022-11-02T12:25:16.000000Z","updated_at": "2022-11-02T12:25:16.000000Z"},),
     *          )
     *      ),
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

    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
        }
        return response()->json(['success' => true, 'data' => $category]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update Category.
     *
     * @OA\Put (
     *     path="/api/category/{id}",
     *     tags={"Category"},
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
     *               @OA\Property(property="errors", type="json", example={"message": {"Category updated successfully."}}),
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
    public function update(UpdateCategoryRequest $updateCategory, string $id): \Illuminate\Http\JsonResponse
    {
        $category = Category::findOrFail($id);
        if ($category) {

            $category->update([
                'name' => $updateCategory->name,
                'description' => $updateCategory->description,
                'is_published'=>$updateCategory->is_published,
            ]);
            return response()->json(['success' => true, 'message' => 'Category updated successfully.']);

        }
        return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
    }

    /**
     * Remove the specified Category from storage.
     *
     * @OA\Delete (
     *     path="/api/category/{id}",
     *     tags={"Category"},
     *     security={{ "apiAuth": {} }},
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *               @OA\Property(property="errors", type="json", example={"message": {"Category deleted successfully."}}),
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
        $category = Category::findOrFail($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found']);
        }

        $isCategoryUsedInProducts =Product::where('category_id', $id)->exists();

        if ($isCategoryUsedInProducts) {
            return response()->json(['success' => false, 'message' => 'Category is in use in product and cannot be deleted'], 403);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }
}
