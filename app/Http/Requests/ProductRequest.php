<?php

namespace Modules\Ecommerce\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'id' => 'required',
            'name' => 'required|string|min:1',
            'publish' => 'nullable|string|in:draft,published',  // Publish status nullable
            'description' => 'required|string', // Assuming a string, modify if it's rich text
            // Validation for images (URLs or files)
            'images' => 'nullable|array', // images can be optional but must be an array
            'images.*' => [
                'required',
                function ($attribute, $value, $fail): void {
                    // Check if the image is a URL
                    if (is_string($value)) {
                        if (! filter_var($value, FILTER_VALIDATE_URL)) {
                            $fail($attribute.' must be a valid URL.');
                        } elseif (! preg_match('/\.(jpg|jpeg|png|webp)$/i', $value)) {
                            $fail($attribute.' must be a valid image URL (jpg, jpeg, png, webp).');
                        }
                    }
                    // Check if the image is a file
                    elseif ($value instanceof \Illuminate\Http\UploadedFile) {
                        if (! $value->isValid()) {
                            $fail($attribute.' is not a valid file.');
                        }
                        if (! in_array($value->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'webp'])) {
                            $fail($attribute.' must be a valid image file (jpg, jpeg, png, webp).');
                        }
                        if ($value->getSize() > 2048 * 1024) { // 2MB max
                            $fail($attribute.' must be less than 2MB.');
                        }
                    }
                    // Invalid if it's neither URL nor file
                    else {
                        $fail($attribute.' must be a valid image URL or file.');
                    }
                },
            ],
            'code' => 'required|string|min:1',
            'sku' => 'required|string|min:1|unique:ecommerce_products,sku,'.$this->id, // Unique SKU
            'quantity' => 'required|integer|min:1',

            // 'colors' => 'required|array|min:1', // Ensuring at least one color is selected
            // 'colors.*' => 'string|max:50', // Assuming color values are strings

            // 'sizes' => 'required|array|min:1', // Ensuring at least one size is selected
            // 'sizes.*' => 'string|max:10', // Assuming size values are short strings

            'tags' => 'array|min:2', // Minimum of 2 tags, but not required
            'tags.*' => 'string|max:255', // Assuming tag values are strings

            // 'gender' => 'required|array|min:1', // Ensuring at least one gender option is selected
            // 'gender.*' => [
            //     'string',
            //     Rule::in(['Men', 'Women', 'Kids']), // Validate against specific values if needed
            // ],

            'variants.gender' => 'sometimes|array',
            'variants.gender.*' => 'string|in:Men,Women,Kids',
            'variants.colors' => 'sometimes|array',
            'variants.colors.*' => 'string|regex:/^#[a-fA-F0-9]{6}$/',
            'variants.sizes' => 'sometimes|array',
            'variants.sizes.*' => 'string|max:10',
            'variants.memories' => 'sometimes|array',
            'variants.memories.*' => 'string|max:10',

            'price' => 'required|numeric|min:1',
            'category' => 'nullable|string|max:255',
            'price_sale' => 'nullable|numeric|min:0',
            'sub_description' => 'nullable|string',
            'taxes' => 'nullable|numeric|min:0|max:100', // Assuming taxes are in percentage
            'sale_label_enabled' => 'boolean',
            'sale_label_content' => 'nullable|string|max:255',
            'new_label_enabled' => 'boolean',
            'new_label_content' => 'nullable|string|max:255',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Convert incoming keys to snake_case
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'images' => $this->input('images'),
            'code' => $this->input('code'),
            'sku' => $this->input('sku'),
            'quantity' => $this->input('quantity'),
            'colors' => $this->input('colors'),
            'sizes' => $this->input('sizes'),
            'tags' => $this->input('tags'),
            'gender' => $this->input('gender'),
            'price' => $this->input('price'),
            'category' => $this->input('category'),
            'price_sale' => $this->input('priceSale'), // Example of snake_case
            'sub_description' => $this->input('subDescription'), // Example of snake_case
            'taxes' => $this->input('taxes'),
            // Flatten the nested sale_label and new_label fields
            'sale_label_enabled' => filter_var($this->input('saleLabel.enabled'), FILTER_VALIDATE_BOOLEAN),
            'sale_label_content' => $this->input('saleLabel.content'),
            'new_label_enabled' => filter_var($this->input('newLabel.enabled'), FILTER_VALIDATE_BOOLEAN),
            'new_label_content' => $this->input('newLabel.content'),
        ]);
    }

    /**
     * Customize the error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required!',
            'description.required' => 'Description is required!',
            'images.required' => 'Images are required!',
            // 'code.required' => 'Product code is required!',
            'sku.required' => 'Product SKU is required!',
            'quantity.required' => 'Quantity is required!',
            // 'colors.required' => 'Choose at least one color!',
            // 'sizes.required' => 'Choose at least one size!',
            'tags.min' => 'Must have at least 2 tags!',
            // 'gender.required' => 'Choose at least one gender!',
            'price.required' => 'Price should not be $0.00!',

            'images.*.required' => 'Each image must be a valid URL or uploaded file.',
            'images.*.url' => 'Each image must be a valid URL.',
            'images.*.file' => 'Each image must be a valid file.',
            'images.*.mimes' => 'Each image must be of type: jpg, jpeg, png, webp.',
            'images.*.max' => 'Each image must be less than 2MB.',
        ];
    }
}
