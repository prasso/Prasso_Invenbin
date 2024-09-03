<?php

namespace Faxt\Invenbin\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Faxt\Invenbin\Models\ErpCategory;
use Faxt\Invenbin\Models\ErpUnitOfMeasure;
use Faxt\Invenbin\Models\ErpProductStatus;
use Faxt\Invenbin\Models\ErpProductType;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    /**
     * Return a list of categories.
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Get(
     *     path="/api/lookups/categories",
     *     summary="Get list of categories",
     *     tags={"Lookups"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of categories",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="category_name", type="string", example="Ink")
     *             )
     *         )
     *     )
     * )
     */
    public function categories()
    {
        $categories = ErpCategory::all(['id', 'category_name']);
        return response()->json($categories);
    }

    /**
     * Return a list of units of measure.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/lookups/units-of-measure",
     *     summary="Get list of units of measure",
     *     tags={"Lookups"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of units of measure",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="ea")
     *             )
     *         )
     *     )
     * )
     */
    public function unitsOfMeasure()
    {
        $units = ErpUnitOfMeasure::all(['id', 'name']);
        return response()->json($units);
    }

    /**
     * Return a list of dimension units.
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Get(
     *     path="/api/lookups/dimension-units",
     *     summary="Get list of dimension units",
     *     tags={"Lookups"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of dimension units",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Centimeter")
     *             )
     *         )
     *     )
     * )
     */
    public function dimensionUnits()
    {
        $dimensionUnits = ErpUnitOfMeasure::all(['id', 'name']);
        return response()->json($dimensionUnits);
    }

    /**
     * @OA\Get(
     *     path="/api/lookups/statuses",
     *     summary="Get list of statuses",
     *     tags={"Lookups"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of statuses",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="Active")
     *             )
     *         )
     *     )
     * )
     */
    public function statuses()
    {
        $statuses = ErpProductStatus::all(['id', 'status']);
        return response()->json($statuses);
    }

    /**
     * @OA\Get(
     *     path="/api/lookups/types",
     *     summary="Get list of types",
     *     tags={"Lookups"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of types",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=2),
     *                 @OA\Property(property="product_type", type="string", example="Component")
     *             )
     *         )
     *     )
     * )
     */
    public function types()
    {
        $types = ErpProductType::all(['id', 'product_type']);
        return response()->json($types);
    }


}
