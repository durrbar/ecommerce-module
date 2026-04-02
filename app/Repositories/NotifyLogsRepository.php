<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Ecommerce\Models\NotifyLogs;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class NotifyLogsRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'notify_type' => 'like',
        'language',
    ];

    public function boot(): void
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
        }
    }

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return NotifyLogs::class;
    }
}
