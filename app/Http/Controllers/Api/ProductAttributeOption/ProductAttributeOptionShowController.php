<?php

namespace App\Http\Controllers\Api\ProductAttributeOption;

use Illuminate\Http\Request;
use App\Models\ProductAttributeOption;
use App\Http\Controllers\Api\ApiController;

class ProductAttributeOptionShowController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');

        $this->middleware('can:view,productAttributeOption')->only('__invoke');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/product_attribute_options/{productAttributeOption}",
     *     summary="Show product attribute option by id",
     *     operationId="getProductAttributeOptionById",
     *     tags={"Product attribute options"},
     *     security={ {"sanctum": {}} },
     *     @OA\Parameter(
     *         name="productAttributeOption",
     *         description="Id of product attribute option",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="number"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/ProductAttributeOption",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="fail",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/AuthenticationException",
     *         ),
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="fail",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/AuthorizationException",
     *         ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="fail",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ModelNotFoundException",
     *         ),
     *     ),
     * )
     */
    public function __invoke(Request $request, ProductAttributeOption $productAttributeOption)
    {
        $includes = explode(',', $request->get('include', ''));

        return $this->showOne(
            $productAttributeOption->loadEagerLoadIncludes($includes)
        );
    }
}
