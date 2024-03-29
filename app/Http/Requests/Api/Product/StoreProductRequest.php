<?php

namespace App\Http\Requests\Api\Product;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreProductGeneralRequest",
 *     required={
 *         "type",
 *         "name",
 *         "category_id",
 *         "price",
 *         "tax",
 *         "description",
 *         "is_variable",
 *         "images",
 *         "tags",
 *     },
 * )
 */
class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @OA\Property(property="type", type="string", enum={"product", "service"}),
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="sku", type="string"),
     * @OA\Property(property="category_id", type="number"),
     * @OA\Property(property="price", type="number"),
     * @OA\Property(property="tax", type="number"),
     * @OA\Property(property="short_description", type="string"),
     * @OA\Property(property="description", type="string"),
     * @OA\Property(property="is_variable", type="boolean"),
     * @OA\Property(property="stock", type="number"),
     * @OA\Property(property="width", type="number"),
     * @OA\Property(property="height", type="number"),
     * @OA\Property(property="length", type="number"),
     * @OA\Property(property="weight", type="number"),
     * @OA\Property(
     *     property="images",
     *     type="object",
     *     required={"attach"},
     *     @OA\Property(
     *         property="attach",
     *         type="array",
     *         @OA\Items(
     *             type="object",
     *             required={"id", "location"},
     *             @OA\Property(property="id", type="number"),
     *             @OA\Property(property="location", type="number"),
     *         ),
     *     ),
     * ),
     * @OA\Property(
     *     property="tags",
     *     type="object",
     *     required={"attach"},
     *     @OA\Property(
     *         property="attach",
     *         type="array",
     *         @OA\Items(
     *             type="number",
     *         ),
     *     ),
     * ),
     * @OA\Property(
     *     property="product_attribute_options",
     *     type="object",
     *     required={"attach"},
     *     @OA\Property(
     *         property="attach",
     *         type="array",
     *         @OA\Items(
     *             type="number",
     *         ),
     *     ),
     * ),
     */
    public function rules()
    {
        filter_var($this->is_variable, FILTER_VALIDATE_BOOLEAN)
            ? $isVariable = true
            : $isVariable = false;

        return [
            'type' => 'required|string|in:' . implode(',', Product::TYPES),
            'name' => 'required|string|max:255|unique:products',
            'sku' => 'nullable|string|max:60|unique:products',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|between:0.00,9999999999.99|regex:/^\d+(\.\d{1,2})?$/',
            'tax' => 'required|numeric|between:0.00,99.99|regex:/^\d+(\.\d{1,2})?$/',
            'stock' => [
                Rule::requiredIf(!$isVariable && $this->type === Product::PRODUCT_TYPE),
                'integer',
                'min:1',
            ],
            'width' => [
                Rule::requiredIf(!$isVariable && $this->type === Product::PRODUCT_TYPE),
                'numeric',
                'between:0.00,9999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'height' => [
                Rule::requiredIf(!$isVariable && $this->type === Product::PRODUCT_TYPE),
                'numeric',
                'between:0.00,9999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'length' => [
                Rule::requiredIf(!$isVariable && $this->type === Product::PRODUCT_TYPE),
                'numeric',
                'between:0.00,9999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'weight' => [
                Rule::requiredIf(!$isVariable && $this->type === Product::PRODUCT_TYPE),
                'numeric',
                'between:0.00,9999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'short_description' => 'nullable|string|max:600',
            'description' => 'nullable|string',
            'is_variable' => 'required|boolean',

            'images' => ['required', 'array:attach'],
            'images.attach' => ['required', 'array', 'min:1', 'max:' . Product::MAX_IMAGES],
            'images.attach.*.id' => 'required|exists:resources,id,obtainable_id,NULL',
            'images.attach.*.location' => [
                'required',
                'integer',
                'min:1',
                'max:' . Product::MAX_IMAGES
            ],

            'tags' => 'array:attach|nullable',
            'tags.attach' => 'array|nullable',
            'tags.attach.*' => 'exists:tags,id',

            'product_attribute_options' => [
                Rule::requiredIf($isVariable),
                'array:attach'
            ],
            'product_attribute_options.attach' => 'array|min:1',
            'product_attribute_options.attach.*' => 'exists:product_attribute_options,id',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'price.regex' => __('The :attribute format must be between :first and :second', [
                'attribute' => 'price',
                'first' => '0.00',
                'second' => '9999999999.99',
            ]),
            'tax.regex' => __('The :attribute format must be between :first and :second', [
                'attribute' => 'tax',
                'first' => '0.00',
                'second' => '99.99',
            ]),
            'width.regex' => __('The :attribute format must be between :first and :second', [
                'attribute' => 'width',
                'first' => '0.00',
                'second' => '9999999999.99',
            ]),
            'height.regex' => __('The :attribute format must be between :first and :second', [
                'attribute' => 'height',
                'first' => '0.00',
                'second' => '9999999999.99',
            ]),
            'length.regex' => __('The :attribute format must be between :first and :second', [
                'attribute' => 'length',
                'first' => '0.00',
                'second' => '9999999999.99',
            ]),
            'weight.regex' => __('The :attribute format must be between :first and :second', [
                'attribute' => 'weight',
                'first' => '0.00',
                'second' => '9999999999.99',
            ]),
        ];
    }
}
