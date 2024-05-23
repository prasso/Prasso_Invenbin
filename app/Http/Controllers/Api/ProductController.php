<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ErpProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Get all products",     
     *     description="Retrieve a list of product records with eager loading of products relationship",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *             @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="guid", type="string", example="11111-11111-11111-11111"),
     *                 @OA\Property(property="product_name", type="string", example="Product A"),
     *                 @OA\Property(property="created_at", type="string", example="01/01/2024")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $products = ErpProduct::paginate(50); 

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    * @OA\Post(
    *     path="/api/products",
    *     tags={"Products"},
    *     summary="Create a new product",
    *     description="Create a new product",
    *     security={{"bearer_token":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/ProductInputOutput")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/ProductInputOutput")
    *     )
    * )
     */
    public function store(Request $request)
    {
         // Validate the incoming request data
         $validatedData = $request->validate([
            'sku' => 'required|string',
            'product_name' => 'required|string',
            'short_description' => 'string',
            'our_price' => 'numeric',
            'retail_price' => 'numeric',
            'weight' => 'numeric',
            'currency_code' => 'string',
            'unit_of_measure_id' => 'numeric',
            'length' => 'numeric',
            'height' => 'numeric',
            'width' => 'numeric',
            'dimension_unit_id' => 'numeric',
            'list_order' => 'numeric',
            'total_rating_votes' => 'numeric',
            'owned_by' => 'numeric',
            'inventory_count' => 'numeric',
            'reorder_point' => 'numeric',
            'product_status_id' => 'numeric',
            'product_type_id' => 'numeric'
        ]);

        // Create a new ErpProduct instance with the validated data
        $product = ErpProduct::create($validatedData);

        // Return a response indicating success
        return $this->sendResponse($product, 'Product created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  string $guid
     * @return \Illuminate\Http\Response
     * 
     * @OA\Get(
     *     path="/api/products/{guid}",
     *     tags={"Products"},
     *     summary="Get a specific product",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="guid",
     *         in="path",
     *         description="GUID of the product",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductInputOutput")
     *     )
     * )
     */
    public function show(Request $request, $guid)
    {
        try {
            // Find the product record by its guid
            $product = ErpProduct::where('guid', $guid)->firstOrFail();

            // Return JSON response with the found product record
            return response()->json(['product' => $product]);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $guid
     * @return \Illuminate\Http\Response
     * @OA\Put(
    *     path="/api/products/{guid}",
    *     tags={"Products"},
    *     summary="Update a product",
    *     description="Update a product",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(
    *         name="guid",
    *         in="path",
    *         description="GUID of the product",
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/ProductInputOutput")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/ProductInputOutput")
    *     )
    * )
     */
    public function update(Request $request, string $guid)
    {
        // Find the product record by its guid
        try {
            $product = ErpProduct::where('guid', $guid)->firstOrFail();

            // Validate the request data
            $request->validate([
                // Your validation rules here
            ]);

            // Remove unnecessary data
            $data = $request->except(['id', 'guid']);

            // Update the product record with the request data
            $product->update($data);

            // Return JSON response indicating success
            return $this->sendResponse($product, 'Product updated successfully.');
        } 
        catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Product not found'], 404);
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
     * @param  string $guid
     * @return \Illuminate\Http\Response
     * @OA\Delete(
     *     path="/api/products/{guid}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     )
     * )
     */
    public function destroy(string $guid)
    {
        $product = ErpProduct::where('guid', $guid)->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        try {
            // Delete related records in product_details table (assuming this is similar to sales_details)
            $product->productDetails()->delete();

            // Delete the product record
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete product'], 500);
        }
    }
}

