<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Ecommerce\Enums\ProductStatus;
use Modules\Ecommerce\Enums\ProductType;

class ProductUpdateRequest extends FormRequest
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
            'name' => ['string', 'max:255'],
            'price' => ['nullable', 'numeric'],
            'sale_price' => ['nullable', 'lte:price'],
            'type_id' => ['exists:Modules\Ecommerce\Models\Type,id'],
            'shop_id' => ['exists:Modules\Ecommerce\Models\Shop,id'],
            'manufacturer_id' => ['nullable', 'exists:Modules\Ecommerce\Models\Manufacturer,id'],
            'author_id' => ['nullable', 'exists:Modules\Ecommerce\Models\Author,id'],
            'categories' => ['exists:Modules\Ecommerce\Models\Category,id'],
            'tags' => ['exists:Modules\Tag\Models\Tag,id'],
            'dropoff_locations' => ['array'],
            'pickup_locations' => ['array'],
            'language' => ['nullable', 'string'],
            'digital_file' => ['array'],
            'product_type' => ['required', new Enum(ProductType::class)],
            'unit' => ['string'],
            'description' => ['nullable', 'string', 'max:10000'],
            'quantity' => ['nullable', 'integer'],
            'sku' => ['string', Rule::unique('variation_options')->where(fn ($query) => $query->whereSku($this->sku))],
            'image' => ['array'],
            'gallery' => ['array'],
            'video' => ['array'],
            'status' => ['string', new Enum(ProductStatus::class)],
            'height' => ['nullable', 'string'],
            'length' => ['nullable', 'string'],
            'width' => ['nullable', 'string'],
            'external_product_url' => ['nullable', 'string'],
            'external_product_button_text' => ['nullable', 'string'],
            'in_stock' => ['boolean'],
            'is_taxable' => ['boolean'],
            'is_digital' => ['boolean'],
            'is_external' => ['boolean'],
            'is_rental' => ['boolean'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
