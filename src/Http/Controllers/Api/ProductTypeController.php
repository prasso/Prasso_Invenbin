<?php

namespace Faxt\Invenbin\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use App\Http\Controllers\Controller;
use Faxt\Invenbin\Models\ErpProductType; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class ProductTypeController extends ErpBaseController 
{ 
    public function __construct(Request $request, AuthManager $auth)
    {
        // Call the parent constructor with the AuthManager, not the Request
        parent::__construct($auth);

        // Use middleware to set the user for each request
        $this->middleware(function ($request, $next) {
            $this->setUser($request); // Set the user based on the current request
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/product-types", 
     *     tags={"ProductTypes"}, 
     *     summary="Get all product types", 
     *     description="Retrieve a list of product types records with eager loading of products relationship",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *             @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="product_type", type="string", example="Assembly")
     *             )
     *         )
     *     )
     *   )
     * )
     */
    public function index()
    {
        $productTypes = ErpProductType::paginate(50);

        return response()->json($productTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    * @OA\Post(
    *     path="/api/product-types", 
    *     tags={"ProductTypes"}, 
    *     summary="Create a new product type", 
    *     description="Create a new product type",
    *     security={{"bearer_token":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/ProductTypeInputOutput") 
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/ProductTypeInputOutput") 
    *     )
    * )
     */
    public function store(Request $request)
    {
         // Validate the incoming request data
         $validatedData = $request->validate([
            'product_type' => 'required|string'
        ]);

        // Create a new ErpProductType instance with the validated data
        $productType = ErpProductType::create($validatedData);

        // Return a response indicating success
        return $this->sendResponse($productType, 'ProductType created successfully.'); 

    }

    /**
     * Display the specified resource.
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/product-types/{id}", 
     *     tags={"ProductTypes"}, 
     *     summary="Get a specific product type", 
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of the product type", 
     *         required=true,
      *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductTypeInputOutput") 
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            // Find the product type record by its id
            $productType = ErpProductType::where('id', $id)->firstOrFail();

            // Load related data
            $productType->load('products');

            // Return JSON response with the found product type record
            return response()->json(['productType' => $productType]); 
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'ProductType not found'], 404); 
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $id
     * @return \Illuminate\Http\Response
     * @OA\Put(
    *     path="/api/product-types/{id}", 
    *     tags={"ProductTypes"}, 
    *     summary="Update a product type", 
    *     description="Update a product type",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="id of the product type", 
    *         required=true,
     *          @OA\Schema(type="integer", format="int64")
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/ProductTypeInputOutput") 
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/ProductTypeInputOutput") 
    *     )
    * )
     */
    public function update(Request $request, string $id)
    {
        // Find the product type record by its id
        try {
            $productType = ErpProductType::where('id', $id)->firstOrFail();

            // Validate the request data
            $request->validate([
                // Your validation rules here
            ]);

            // Remove unnecessary data
            $data = $request->except(['id']);

            // Update the product type record with the request data
            $productType->update($data);

            // Return JSON response indicating success
            return $this->sendResponse($productType, 'ProductType updated successfully.'); 
        } 
        catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'ProductType not found'], 404); 
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
     *     path="/api/product-types/{id}", 
     *     tags={"ProductTypes"}, 
     *     summary="Delete a product type", 
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product type", 
     *         required=true,
      *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="ProductType deleted successfully" 
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $productType = ErpProductType::where('id', $id)->first();
        if (!$productType) {
            return response()->json(['message' => 'ProductType not found'], 404); 
        }

        try {

            // Delete the product type record
            $productType->delete();

            return response()->json(['message' => 'ProductType deleted successfully'], 200); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete product type'], 500); 
        }
    }
}
