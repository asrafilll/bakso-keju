<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManufactureProductStoreRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'created_at' => [
                'required',
                'date_format:Y-m-d H:i:s',
            ],
            'branch_id' => [
                'required',
                'string',
            ],
            'line_product_components' => [
                'required',
                'array',
            ],
            'line_product_components.*.product_component_id' => [
                'required',
                'string',
            ],
            'line_product_components.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'line_products' => [
                'required',
                'array',
            ],
            'line_products.*.product_id' => [
                'required',
                'string',
            ],
            'line_products.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
