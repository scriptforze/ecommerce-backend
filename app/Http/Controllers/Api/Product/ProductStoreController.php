<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Actions\Product\StoreProduct;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\Product\StoreProductRequest;

class ProductStoreController extends ApiController
{
    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;

        $this->middleware('auth:sanctum');

        $this->middleware('can:create,' . Product::class)->only('__invoke');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products/general",
     *     summary="Save product general information",
     *     description="<strong>Method:</strong> saveProductGeneral<br/><strong>Includes:</strong> status, images, stock_images, category, tags, product_attribute_options, product_stocks",
     *     operationId="saveProductGeneral",
     *     tags={"Products"},
     *     security={ {"sanctum": {}} },
     *     @OA\Parameter(
     *         name="include",
     *         description="Relationships of resource",
     *         required=false,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lang",
     *         description="Code of language",
     *         required=false,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 ref="#/components/schemas/StoreProductGeneralRequest",
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Product",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="fail",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/BadRequestException",
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
     *         response="422",
     *         description="fail",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ValidationException",
     *         ),
     *     ),
     * )
     */
    public function __invoke(StoreProductRequest $request)
    {
        $includes = explode(',', $request->get('include', ''));

        DB::beginTransaction();
        try {
            $this->product = app(StoreProduct::class)(
                $this->product->setCreate($request)
            );
            DB::commit();

            return $this->showOne(
                $this->product->scopeWithEagerLoading(
                    query: null,
                    includes: $includes,
                )
            );
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
