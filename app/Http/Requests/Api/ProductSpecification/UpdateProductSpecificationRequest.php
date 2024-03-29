<?php

namespace App\Http\Requests\Api\ProductSpecification;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema()
 */
class UpdateProductSpecificationRequest extends FormRequest
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
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="value", type="string"),
     */
    public function rules()
    {
        return [
            'name' => 'string|max:255|nullable',
            'value' => 'string|max:255|nullable',
        ];
    }
}
