<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventoryStoreRequest extends FormRequest
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
            'product_id' => [
                'required',
                'string',
                Rule::exists('products', 'id'),
            ],
            'branch_id' => [
                'required',
                'string',
                Rule::exists('branches', 'id'),
            ],
            'quantity' => [
                'required',
                'integer',
            ],
            'note' => [
                'nullable',
                'string',
            ],
        ];
    }
}
