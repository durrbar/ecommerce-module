<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait Excludable
{
    /**
     * Exclude an array of elements from the result.
     */
    #[Scope]
    public function exclude(Builder $query, array|string $columns): Builder
    {
        return $query->select(array_diff($this->getTableColumns(), (array) $columns));
    }

    /**
     * Get the array of columns
     */
    private function getTableColumns(): array
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
