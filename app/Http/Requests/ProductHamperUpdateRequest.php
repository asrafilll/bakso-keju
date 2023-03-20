<?php

namespace App\Http\Requests;

use App\Models\OrderSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductHamperUpdateRequest extends FormRequest
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
        ];

        return $rules;
    }
}
