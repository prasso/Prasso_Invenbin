<?php

namespace Faxt\Invenbin\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Faxt\Invenbin\Models\ErpCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
/**
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class CategoryController extends ErpBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     summary="Get all categories",     
     *     description="Retrieve a list of categories records with eager loading of products relationship",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *             @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="2"),
     *                 @OA\Property(property="category_name", type="string", example="Cat A"),
     *                 @OA\Property(property="parent", type="string", example="1")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $cats = ErpCategory::with('products')->paginate(50); 

        return response()->json($cats);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    * @OA\Post(
    *     path="/api/categories",
    *     tags={"Categories"},
    *     summary="Create a new category",
    *     description="Create a new category",
    *     security={{"bearer_token":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/CategoryInput")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/CategoryOutput")
    *     )
    * )
     */
    public function store(Request $request)
    {
         // Validate the incoming request data
         $validatedData = $request->validate([
            'category_name' => 'required|string',
            'short_description' => 'required|string'
        ]);

        // Create a new ErpCategory instance with the validated data
        $category = ErpCategory::create($validatedData);

        // Return a response indicating success
        return $this->sendResponse($category, 'Category created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Get a specific category",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the category",
     *         required=true,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/CategoryOutput")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            // Find the category record by its id
            $category = ErpCategory::where('id', $id)->firstOrFail();
           
            // Load related data
            $category->load('products');
        
            // Return JSON response with the found category record
            return response()->json(['category' => $category]);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $id
     * @return \Illuminate\Http\Response
     * @OA\Put(
    *     path="/api/categories/{id}",
    *     tags={"Categories"},
    *     summary="Update a category",
    *     description="Update a category",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="ID of the category",
    *         required=true,
     *          @OA\Schema(type="integer", format="int64")
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/CategoryInput")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/CategoryOutput")
    *     )
    * )
     */
    public function update(Request $request, string $id)
    {
        // Find the category record by its id
        try {
            $category = ErpCategory::where('id', $id)->firstOrFail();

            // Validate the request data
            $request->validate([
                // Your validation rules here
            ]);

            Log::debug(json_encode($request));
            // Remove unnecessary data
            $data = $request->except(['id']);
            // Update the category record with the request data
            $category->update($data);

            // Return JSON response indicating success
            return $this->sendResponse($category, 'Category updated successfully.');
        } 
        catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Category not found'], 404);
        } catch (\Exception $exception) {
            // Log the exception for further investigation
            Log::error($exception);
            // Return a generic error message
            return response()->json(['message' => 'An error occurred while processing your request'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the category",
     *         required=true,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Category deleted successfully"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $category = ErpCategory::where('id', $id)->first();
        
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        //any child categories? don't delete it if so
        if ($category->children())
        {
            return response()->json(['message' => 'Can not delete, child categories exist'], 500);
        }
        //any products using this? don't delete it if so
        if ($category->products())
        {
            return response()->json(['message' => 'Can not delete, products are using'], 500);
        }
        try {
            // Delete the category record
            $category->delete();

            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete category'], 500);
        }
    }
}

