<?php

namespace Faxt\Invenbin\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use App\Http\Controllers\Controller;
use  Faxt\Invenbin\Models\ErpProduct;
use  Faxt\Invenbin\Models\ErpComponent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ProductController extends ErpBaseController
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
     * 
    * @OA\Post(
    *     path="/api/products",
    *     tags={"Products"},
    *     summary="Create a new product",
    *     description="Create a new product",
    *     security={{"bearer_token":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/ProductInput")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/ProductOutput")
    *     ),
    *     @OA\Response(
    *         response=422,
    *         description="Validation error",
    *         @OA\JsonContent(
    *             type="object",
    *             @OA\Property(property="message", type="string", example="The given data was invalid."),
    *             @OA\Property(property="errors", type="object")
    *         )
    *     )
    * )
     */
    public function store(Request $request)
    { 
        try {

        // Validate product data
        $validatedData = $this->validateProductData($request);

        // Create an ErpProduct using the factory method
        $product = $this->createErpProduct($validatedData);

        // Attach the category to the product using the pivot table 'erp_product_category_maps'
        $product->categories()->attach($request->input('category_id'));
        $product->isSingleRecord = true;

        // Optionally, you can fetch the product with its relationships loaded
        $product->load('categories'); // Ensure categories are loaded

        // Return a response indicating success
        return $this->sendResponse($product, 'Product created successfully.');
    } catch (ValidationException $validationException) {
        // Return validation errors as JSON
        return response()->json(['The given data was invalid. ' => $validationException->errors()], 422);
    } catch (\Exception $exception) {
        Log::info($exception);
        // Return an error message if the process fails
        return response()->json(['error' => 'Process failed, check the guid'], 500);
    }
    }

    /**
     * Store a newly created resource in storage.
     * The model is Component and the Component is only stored in the ErpComponent table if a 
     * Bill of Materials id is passed.  If no Bill of Materials, no component table record. 
     * However, in both cases, our component will be stored as an ErpProduct record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
    * @OA\Post(
    *     path="/api/products/components",
    *     tags={"Products"},
    *     summary="Create a new product and component if bom id passed",
    *     description="Create a new product and component if bom id passed",
    *     security={{"bearer_token":{}}},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(ref="#/components/schemas/ProductComponentInput")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/ProductOutput")
    *     )
    * )
    */
    public function createProductOfComponentType(Request $request)
    {

        // Validate product data
        $validatedData = $this->validateProductData($request);

        // Create an ErpProduct using the factory method
        $erpProduct = $this->createErpProduct($validatedData);

        // Check if a BOM ID is provided
        if ($request->filled('erp_bom_id')) {
            // Create a Component only if BOM ID is provided
            ErpComponent::create([
                'erp_product_id' => $erpProduct->id,
                'item_description' => $validatedData['short_description'],
                'adjustment_units' => 1, // Example default value, adjust as necessary
                'erp_bom_id' => $validatedData['erp_bom_id'],
            ]);
        }

        return response()->json(['message' => 'ErpProduct created successfully', 'erpProduct' => $erpProduct], 201);
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
     *         @OA\JsonContent(ref="#/components/schemas/ProductOutput")
     *     )
     * )
     */
    public function show(Request $request, $guid)
    {
        try {
            // Find the product record by its guid
            $product = ErpProduct::where('guid', $guid)->firstOrFail();
           
            $product->isSingleRecord = true;
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
    *         @OA\JsonContent(ref="#/components/schemas/ProductInput")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/ProductOutput")
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
            $product->isSingleRecord = true;
            
            $attributes = $product->attributesToArray();

            // Return JSON response indicating success
            return $this->sendResponse($attributes, 'Product updated successfully.');
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
     *         name="guid",
     *         in="path",
     *         description="GUID of the product",
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
            // Check if the product has any related product descriptors before attempting to delete
            if ($product->descriptors()->exists()) {
                // Delete related records in product_details table
                $product->descriptors()->delete();
            }
    
            // Delete the product record
            $product->delete();
    
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete product: ' . $e->getMessage()], 500);
        }
    }

    private function createErpProduct($validatedData)
    {
        $erpProductData = [
            'product_name' => $validatedData['product_name'],
            'short_description' => $validatedData['short_description'],
            'product_type_id' => 1, // this is component product type
            'product_status_id' => 1, // this is active product status
            'updated_by' => $this->user->id,
        ];
    
         // Conditionally add 'sku' if it exists and is not null
        if (isset($validatedData['sku']) && !is_null($validatedData['sku'])) {
            $erpProductData['sku'] = $validatedData['sku'];
        }

        // Conditionally add 'our_price' if it exists and is not null
        if (isset($validatedData['our_price']) && !is_null($validatedData['our_price'])) {
            $erpProductData['our_price'] = $validatedData['our_price'];
        }
        // Conditionally add 'retail_price' if it exists and is not null
        if (isset($validatedData['retail_price']) && !is_null($validatedData['retail_price'])) {
            $erpProductData['retail_price'] = $validatedData['retail_price'];
        }

        return ErpProduct::createWithDefaults($erpProductData);
    }
    

    private function validateProductData(Request $request)
    {
        return $request->validate([
            'product_name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'erp_bom_id' => 'nullable|exists:erp_boms,id',
        ]);
        
    }

}

