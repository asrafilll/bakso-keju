<?php

namespace App\Http\Requests;

use App\Models\OrderSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
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
        $rules = [
            'name' => [
                'required',
                'string',
            ],
            'price' => [
                'required',
                'integer',
                'min:0',
            ],
            'product_category_id' => [
                'nullable',
                'string',
                Rule::exists('product_categories', 'id'),
            ],
            'prices' => [
                'required',
                'array',
            ],
        ];

        if (OrderSource::count() > 0) {
            $rules += [
                'prices.*.order_source_id' => [
                    'required',
                    'string',
                ],
                'prices.*.price' => [
                    'required',
                    'integer',
                    'min:0',
                ],
            ];
        }

        return $rules;
    }
}
