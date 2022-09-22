<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductImportStoreRequest extends FormRequest
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
            'product_category_id' => [
                'nullable',
                'string',
                Rule::exists('product_categories', 'id')->whereNotNull('parent_id'),
            ],
            'file' => [
                'required',
                'file',
                'mimes:csv',
            ],
        ];
    }
}
