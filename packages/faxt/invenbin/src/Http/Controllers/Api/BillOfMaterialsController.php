<?php

namespace Faxt\Invenbin\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Faxt\Invenbin\Models\ErpBillOfMaterials; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class BillOfMaterialsController extends Controller 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/bom", 
     *     tags={"BillOfMaterials"}, 
     *     summary="Get all bills of material", 
     *     description="Retrieve a list of bills of material records with eager loading of products relationship",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *             @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="guid", type="string", example="abc123"),
     *                 @OA\Property(property="bom_name", type="string", example="cup with logo")
     *             )
     *         )
     *     )
     *   )
     * )
     */
    public function index()
    {
        $billOfMaterialss = ErpBillOfMaterials::paginate(50);

        return response()->json($billOfMaterialss);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    * @OA\Post(
    *     path="/api/bom", 
    *     tags={"BillOfMaterials"}, 
    *     summary="Create a new bill of material", 
    *     description="Create a new bill of material",
    *     security={{"bearer_token":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/BillOfMaterialsInput") 
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/BillOfMaterialsOutput") 
    *     )
    * )
     */
    public function store(Request $request)
    {
         // Validate the incoming request data
         $validatedData = $request->validate([
            'erp_product_id' => 'required|numeric', 
            'bom_name' => 'required|string'
        ]);


        // Create a new ErpBillOfMaterials instance with the validated data
        $billOfMaterials = ErpBillOfMaterials::create($validatedData);

        // Return a response indicating success
        return $this->sendResponse($billOfMaterials, 'BillOfMaterials created successfully.'); 

    }

    /**
     * Display the specified resource.
     *
     * @param  string $guid
     * @return \Illuminate\Http\Response
     * @OA\Get(
     *     path="/api/bom/{guid}", 
     *     tags={"BillOfMaterials"}, 
     *     summary="Get a specific bill of material", 
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="guid",
     *         in="path",
     *         description="GUID of the bill of material", 
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/BillOfMaterialsOutput") 
     *         )
     *     )
     * )
     */
    public function show(Request $request, $guid)
    {
        try {
            // Find the bill of material record by its guid
            $billOfMaterials = ErpBillOfMaterials::where('guid', $guid)->firstOrFail();

            // Load related data
            $billOfMaterials->load('product');

            // Return JSON response with the found bill of material record
            return response()->json(['billOfMaterials' => $billOfMaterials]); 
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'BillOfMaterials not found'], 404); 
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $guid
     * @return \Illuminate\Http\Response
     * @OA\Put(
    *     path="/api/bom/{guid}", 
    *     tags={"BillOfMaterials"}, 
    *     summary="Update a bill of material", 
    *     description="Update a bill of material",
    *     security={{"bearer_token":{}}},
    *     @OA\Parameter(
    *         name="guid",
    *         in="path",
    *         description="GUID of the bill of material", 
    *         required=true,
    *         @OA\Schema(
    *             type="string"
    *         )
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/BillOfMaterialsInput") 
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/BillOfMaterialsOutput") 
    *     )
    * )
     */
    public function update(Request $request, string $guid)
    {
        // Find the bill of material record by its guid
        try {
            $billOfMaterials = ErpBillOfMaterials::where('guid', $guid)->firstOrFail();

            // Validate the request data
            $request->validate([
                // Your validation rules here
            ]);

            // Remove unnecessary data
            $data = $request->except(['id', 'guid']);

            // Update the bill of material record with the request data
            $billOfMaterials->update($data);

            // Return JSON response indicating success
            return $this->sendResponse($billOfMaterials, 'BillOfMaterials updated successfully.'); 
        } 
        catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'BillOfMaterials not found'], 404); 
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
     *     path="/api/bom/{guid}", 
     *     tags={"BillOfMaterials"}, 
     *     summary="Delete a bill of material", 
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the bill of material", 
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="BillOfMaterials deleted successfully" 
     *     )
     * )
     */
    public function destroy(string $guid)
    {
        $billOfMaterials = ErpBillOfMaterials::where('guid', $guid)->first();

        if (!$billOfMaterials) {
            return response()->json(['message' => 'BillOfMaterials not found'], 404); 
        }

        try {
            // Delete related records in bom_details table (assuming this is similar to sales_details)
            $billOfMaterials->billOfMaterialsDetails()->delete(); 

            // Delete the bill of material record
            $billOfMaterials->delete();

            return response()->json(['message' => 'BillOfMaterials deleted successfully'], 200); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete bill of material'], 500); 
        }
    }
}
