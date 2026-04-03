<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

use Modules\Ecommerce\Models\Question;

class QuestionAnswered
{
    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Question $question) {}
}
