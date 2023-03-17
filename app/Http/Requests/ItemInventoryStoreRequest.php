<?php

namespace App\Http\Requests;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemInventoryStoreRequest extends FormRequest
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
            'item_id' => [
                'required',
                'string',
                Rule::exists('items', 'id'),
            ],
            'branch_id' => [
                'required',
                'string',
                Rule::exists('branches', 'id'),
            ],
            'quantity' => [
                'required',
                'integer',
                'min:0',
            ],
        ];

        if ($this->user()->can(PermissionEnum::create_negative_quantity_inventory()->value)) {
            $rules['quantity'] = [
                'required',
                'integer',
            ];
        }

        return $rules;
    }
}
