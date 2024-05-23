<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ErpProductStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class ProductStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/product-statuses", 
     *     tags={"ProductStatuses"}, 
     *     summary="Get all statuses that apply to products", 
     *     description="Retrieve a list of product status records",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *             @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="Active")
     *             )
     *         )
     *     )
     *   )
     * )
     */
    public function index()
    {
        $productStatuses = ErpProductStatus::get();

        return response()->json($productStatuses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @OA\Post(
     *     path="/api/product-statuses", 
     *     tags={"ProductStatuses"}, 
     *     summary="Create a new product status", 
     *     description="Create a new product status",
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductStatusInputOutput") 
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductStatusInputOutput") 
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'status' => 'required|string|max:255'
        ]);

        $productStatus = ErpProductStatus::create($validatedData);

        return response()->json($productStatus, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     *
     * @OA\Get(
     *     path="/api/product-statuses/{id}", 
     *     tags={"ProductStatuses"}, 
     *     summary="Get a specific product status", 
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of the product status", 
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductStatusInputOutput") 
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            // Find the product type record by its id
            $productStatus = ErpProductStatus::where('id', $id)->firstOrFail();


            // Return JSON response with the found product type record
            return response()->json(['productStatus' => $productStatus]); 
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'ProductStatus not found'], 404); 
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $id
     * @return \Illuminate\Http\Response
     * @OA\Put(
    *     path="/api/product-statuses/{id}", 
    *     tags={"ProductStatuses"}, 
    *     summary="Update a product status", 
    *     description="Update a product status",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         description="id of the product status", 
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/ProductStatusInputOutput") 
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/ProductStatusInputOutput") 
    *     )
    * )
     */
    public function update(Request $request, $id)
    {

        // Find the product status record by its id
        try {
            $productStatus = ErpProductStatus::where('id', $id)->firstOrFail();

            // Validate the request data
            $request->validate([
                'status' => 'required|string|max:255'
            ]);

            // Remove unnecessary data
            $data = $request->except(['id']);

            // Update the product status record with the request data
            $productStatus->update($data);

            // Return JSON response indicating success
            return $this->sendResponse($productStatus, 'ProductStatus updated successfully.'); 
        } 
        catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'ProductStatus not found'], 404); 
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
     *     path="/api/product-statuses/{id}", 
     *     tags={"ProductStatuses"}, 
     *     summary="Delete a product status", 
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product status", 
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="ProductStatus deleted successfully" 
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $productStatus = ErpProductStatus::where('id', $id)->first();

        if (!$productStatus) {
            return response()->json(['message' => 'ProductStatus not found'], 404); 
        }

        try {
            // Delete related records in product_status_details table (assuming this is similar to sales_details)
            $productStatus->productStatusDetails()->delete(); 

            // Delete the product status record
            $productStatus->delete();

            return response()->json(['message' => 'ProductStatus deleted successfully'], 200); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete product status'], 500); 
        }
    }

}