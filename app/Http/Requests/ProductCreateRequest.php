<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;
use Modules\Ecommerce\Enums\ProductStatus;
use Modules\Ecommerce\Enums\ProductType;

class ProductCreateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric'],
            'sale_price' => ['nullable', 'lte:price'],
            'type_id' => ['required', 'exists:Modules\Ecommerce\Models\Type,id'],
            'shop_id' => ['required', 'exists:Modules\Ecommerce\Models\Shop,id'],
            'manufacturer_id' => ['nullable', 'exists:Modules\Ecommerce\Models\Manufacturer,id'],
            'author_id' => ['nullable', 'exists:Modules\Ecommerce\Models\Author,id'],
            'product_type' => ['required', new Enum(ProductType::class)],
            'categories' => ['array'],
            'tags' => ['array'],
            'language' => ['nullable', 'string'],
            'dropoff_locations' => ['array'],
            'pickup_locations' => ['array'],
            'digital_file' => ['array'],
            'variations' => ['array'],
            'variation_options' => ['array'],
            'quantity' => ['nullable', 'integer'],
            'unit' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:10000'],
            'sku' => ['string', 'unique:variation_options,sku'],
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
            'variation_options.upsert.*.sku' => ['string', 'unique:variation_options,sku'],
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
