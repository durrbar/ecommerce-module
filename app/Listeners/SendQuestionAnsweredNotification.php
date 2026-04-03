<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Events\QuestionAnswered;
use Modules\Ecommerce\Notifications\NotifyQuestionAnswered;
use Modules\Notification\Enums\EventType;
use Modules\Notification\Traits\SmsTrait;
use Modules\User\Models\User;

class SendQuestionAnsweredNotification implements ShouldQueue
{
    use SmsTrait;

    /**
     * Handle the event.
     *
     */
    public function handle(QuestionAnswered $event): void
    {
        $emailReceiver = $this->getWhichUserWillGetEmail(EventType::QuestionAnswered->value, $event->question->language ?? DEFAULT_LANGUAGE);
        if ($emailReceiver['customer'] && $event->question->customer) {
            $customer = User::findOrFail($event->question->user_id);
            $customer->notify(new NotifyQuestionAnswered($event->question));
        }
    }
}
