<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
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
            'order_source_id' => [
                'required',
                'string',
            ],
            'reseller_id' => [
                'nullable',
                'string',
            ],
            'customer_name' => [
                'required',
                'string',
            ],
            'customer_phone_number' => [
                'nullable',
                'numeric',
            ],
            'line_items' => [
                'nullable',
                'array',
            ],
            'line_items.*.product_id' => [
                'required_with:line_items',
                'string',
            ],
            'line_items.*.quantity' => [
                'required_with:line_items',
                'integer',
            ],
            'line_hampers' => [
                'nullable',
                'array',
            ],
            'line_hampers.*.product_hamper_id' => [
                'required_with:line_hampers',
                'string',
            ],
            'line_hampers.*.quantity' => [
                'required_with:line_hampers',
                'integer',
            ],
        ];
    }
}
