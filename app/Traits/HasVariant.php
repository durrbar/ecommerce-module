<?php

namespace Modules\Ecommerce\Traits;

use Illuminate\Database\Eloquent\Model;
use Modules\Ecommerce\Models\Variant;

trait HasVariant
{
    /**
     * Sync the given variants with the given model.
     *
     * @param Model $model
     * @param array $variants
     * @return array The IDs of the synced variants.
     */
    public function syncVariants(Model $model, array $variants = []): array
    {
        $syncData = [];

        foreach ($variants as $type => $values) {
            if (!is_array($values) || empty($values)) {
                continue;
            }

            // Retrieve existing variants by type and name
            $existingVariants = Variant::where('type', $type)
                ->whereIn('name', $values)
                ->pluck('id', 'name')
                ->toArray();

            foreach ($values as $value) {
                // Use existing variant ID or create a new one
                if (!isset($existingVariants[$value])) {
                    $variant = Variant::create(['name' => $value, 'type' => $type]);
                    $existingVariants[$value] = $variant->id;
                }

                $syncData[] = $existingVariants[$value];
            }
        }

        // Sync only the collected variant IDs with the model
        $model->variants()->sync($syncData);

        return $syncData; // Returning synced variant IDs for debugging/logging
    }
}
