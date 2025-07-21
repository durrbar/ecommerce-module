<?php

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

    public function boot()
    {
        try {
            $this->pushCriteria(app(RequestCriteria::class));
        } catch (RepositoryException $e) {
        }
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return NotifyLogs::class;
    }
}
