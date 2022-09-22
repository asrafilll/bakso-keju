<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchUpdateRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
            ],
            'phone' => [
                'nullable',
                'string',
            ],
            'order_number_prefix' => [
                'required',
                'alpha_num',
            ],
            'next_order_number' => [
                'required',
                'integer',
                'min:1',
            ],
            'purchase_number_prefix' => [
                'required',
                'alpha_num',
            ],
            'next_purchase_number' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * @override
     * @return array<string, string>
     */
    public function validated()
    {
        return array_filter(parent::validated(), fn ($row) => !is_null($row));
    }
}
