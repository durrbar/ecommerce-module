<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;

trait Excludable
{
    /**
     * Exclude an array of elements from the result.
     *
     * @return mixed
     */
    #[Scope]
    public function exclude($query, $columns)
    {
        return $query->select(array_diff($this->getTableColumns(), (array) $columns));
    }

    /**
     * Get the array of columns
     *
     * @return mixed
     */
    private function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
}
