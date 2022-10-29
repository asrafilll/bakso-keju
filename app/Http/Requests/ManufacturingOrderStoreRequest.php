<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManufacturingOrderStoreRequest extends FormRequest
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
            'line_items' => [
                'required',
                'array',
            ],
            'line_items.*.product_component_id' => [
                'required',
                'string',
            ],
            'line_items.*.price' => [
                'required',
                'integer',
                'min:1',
            ],
            'line_items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
            'line_items.*.total_weight' => [
                'required',
                'numeric',
                'min:1',
            ],
        ];
    }
}
