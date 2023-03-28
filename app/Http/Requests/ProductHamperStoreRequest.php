<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductHamperStoreRequest extends FormRequest
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
            'branch_id' => [
                'required',
                'string',
                Rule::exists('branches', 'id'),
            ],
            'name' => [
                'required',
                'string',
            ],
            'charge' => [
                'required',
                'integer',
                'min:0',
            ],
            'products' => [
                'required',
                'array',
            ],
            'products.*.product_id' => [
                'required',
                'string',
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
