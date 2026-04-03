<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'default' => ['boolean'],
            'address' => ['required', 'array'],
            'customer_id' => ['required', 'exists:Modules\Ecommerce\Models\User,id'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {

        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
