<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTermsAndConditionsRequest extends FormRequest
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
        // $language = $this->language ?? DEFAULT_LANGUAGE;

        $rules = [
            'title' => ['required', 'string'],
            'description' => ['required', 'string', 'max:10000'],
            'slug' => ['nullable', 'string'],
            'language' => ['nullable', 'string'],
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
