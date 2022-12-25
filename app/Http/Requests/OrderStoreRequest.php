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
                'required',
                'array',
            ],
            'line_items.*.product_id' => [
                'required',
                'string',
            ],
            'line_items.*.quantity' => [
                'required',
                'integer',
            ],
        ];
    }
}
