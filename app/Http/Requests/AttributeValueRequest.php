<?php

namespace Modules\Ecommerce\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttributeValueRequest extends FormRequest
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
            'value' => ['required', 'string', 'max:255'],
            'meta' => ['nullable', 'string'],
            'price' => ['numeric'],
            'shop_id' => ['required', 'exists:Modules\Ecommerce\Models\Shop,id'],
            'attribute_id' => ['required', 'exists:Modules\Ecommerce\Models\Attribute,id'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
