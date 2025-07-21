<?php

namespace Modules\Ecommerce\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\Ecommerce\Models\Feedback;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

class FeedbackRepository extends BaseRepository
{
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
        return Feedback::class;
    }
}
