<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ErpProduct;
use App\Models\ErpProductUsageLog;

class InventoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/inventory",
     *      operationId="getInventory",
     *      tags={"Inventory"},
     *      summary="Get inventory levels for all products",
     *     security={{"bearer_token":{}}},
     *      description="Returns a list of all products along with their inventory levels.",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", description="Product ID"),
     *                  @OA\Property(property="product_name", type="string", description="Product name"),
     *                  @OA\Property(property="inventory_count", type="integer", description="Inventory count")
     *              )
     *          )
     *      )
     * )
     * Display a listing of the inventory levels for all products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve all products with their inventory counts
        $inventory = ErpProduct::select('id', 'product_name', 'inventory_count')->paginate(50);
        
        // Return JSON response with inventory data
        return response()->json($inventory);
    }

    

     /**
     * @OA\Get(
     *     path="/api/inventory/{id}",
     *     summary="Display the inventory level for a specific product.",
     *     description="Returns the inventory level for a product identified by its ID.",
     *     tags={"Inventory"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="Product ID"),
     *                 @OA\Property(property="product_name", type="string", description="Product Name"),
     *                 @OA\Property(property="inventory_count", type="integer", description="Inventory Count")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product not found.")
     *         )
     *     )
     * )
     * Display the inventory level for a specific product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Find the product by its ID
        $product = ErpProduct::where('id',$id)->select('id', 'product_name', 'inventory_count')->get();

        // Check if the product exists
        if (!$product) {
            // Return a 404 Not Found response if the product is not found
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Return JSON response with inventory data for the specific product
        return response()->json($product);
    }

    /**
     * @OA\Put(
     *     path="/api/inventory/{id}",
     *     summary="Update the inventory level for a specific product.",
     *     description="Adjusts the inventory level based on the incoming adjustment value and returns the updated product details.",
     *     tags={"Inventory"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Adjustment value for the inventory count",
     *         @OA\JsonContent(
     *             required={"adjustment"},
     *             @OA\Property(property="adjustment", type="number", format="float", example=10, description="The adjustment amount to update the inventory level")
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"adjustment", "adjustment_type","reason"},
     *             @OA\Property(property="adjustment", type="integer"),
     *             @OA\Property(property="adjustment_type", type="string", example="+"),
     *             @OA\Property(property="reason", type="string", example="return to stock"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", description="Product ID"),
     *             @OA\Property(property="product_name", type="string", description="Product Name"),
     *             @OA\Property(property="inventory_count", type="integer", description="Updated Inventory Count")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product not found.")
     *         )
     *     )
     * )
     * 
     * It is convention that the update number received is an adjustment amount, 
     * not an inventory count. adjust the inventory based on the incoming value and the adjustment_type (+ or -).
     *  as part of this code, check reorder levels 
     * and send notifications if any defined should the reorder point be reached
    * Update the inventory level for a specific product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find the product by its ID
        $product = ErpProduct::find($id);

        // Check if the product exists
        if (!$product) {
            // Return a 404 Not Found response if the product is not found
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Validate the request data
        $request->validate([
            'adjustment' => 'required|numeric',
            'adjustment_type' => 'required|string', // addition or removal +/-
            'reason'  => 'required|string',
        ]);

        // Adjust the inventory level based on the incoming adjustment value
        $adjustment = $request->input('adjustment');
        $adjustment_type =  $request->input('adjustment_type');
        if ($adjustment_type == '-')
          {  $newInventoryLevel = $product->inventory_count - $adjustment;}
        else
          {  $newInventoryLevel = $product->inventory_count + $adjustment;}

        // Update the inventory level for the product
        $product->inventory_count = $newInventoryLevel;
        $product->save();

        //usage log
        ErpProductUsageLog::create($request->all());

        // Check if the new inventory level falls below the reorder point
        if ($newInventoryLevel <= $product->reorder_point) {
            // Send notifications for reorder point reached (example)
            // You can implement your notification logic here
            // For example, send an email to notify someone about the low inventory
            // This is just a placeholder notification example
            // Replace it with your actual notification logic
            $notificationMessage = "Reorder point reached for product: " . $product->product_name;
            // Example of sending email notification
            // mail('admin@example.com', 'Inventory Reorder Notification', $notificationMessage);
            // Example of sending notification to a Slack channel
            // \Notification::route('slack', config('services.slack.webhook'))->notify(new InventoryReorderNotification($notificationMessage));
        }

        // Select only the required fields
        $selectedProduct = ErpProduct::select('id', 'product_name', 'inventory_count')->find($id);

        // Return the selected product data
        return response()->json($selectedProduct);
    }

    /**
     * Set the reorder point for a specific product and trigger notifications if necessary.
     *
     * @OA\Post(
     *      path="/api/products/{id}/reorder-point",
     *      operationId="setReorderPoint",
     *      tags={"Products"},
     *      summary="Set reorder point for a product",
     *     security={{"bearer_token":{}}},
     *      description="Set the reorder point for a specific product and trigger notifications if necessary.",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of the product",
     *          required=true,
     *          @OA\Schema(type="integer", format="int64")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Reorder point data",
     *          @OA\JsonContent(
     *              required={"reorder_point"},
     *              @OA\Property(property="reorder_point", type="number", example="10")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success response",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Reorder point updated successfully."),
     *              @OA\Property(property="product", ref="#/components/schemas/ProductInputOutput")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Product not found.")
     *          )
     *      )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setReorderPoint(Request $request, $id)
    {
        // Find the product by its ID
        $product = ErpProduct::find($id);

        // Check if the product exists
        if (!$product) {
            // Return a 404 Not Found response if the product is not found
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Validate the request data
        $request->validate([
            'reorder_point' => 'required|numeric', // Assuming the reorder point is a numeric value
        ]);

        // Set the reorder point for the product
        $product->reorder_point = $request->input('reorder_point');
        $product->save();

        // Check if the current inventory level falls below the newly set reorder point
        if ($product->inventory_count <= $product->reorder_point) {
            // Send notifications for reorder point reached (example)
            // You can implement your notification logic here
            // For example, send an email to notify someone about the low inventory
            // This is just a placeholder notification example
            // Replace it with your actual notification logic
            $notificationMessage = "Reorder point reached for product: " . $product->product_name;
            // Example of sending email notification
            // mail('admin@example.com', 'Inventory Reorder Notification', $notificationMessage);
            // Example of sending notification to a Slack channel
            // \Notification::route('slack', config('services.slack.webhook'))->notify(new InventoryReorderNotification($notificationMessage));
        }

        // Return a success response with the entire product object
        return response()->json(['message' => 'Reorder point updated successfully.', 'product' => $product]);
    }
    
}
