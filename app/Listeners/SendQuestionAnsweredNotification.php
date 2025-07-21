<?php

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
     * @return void
     */
    public function handle(QuestionAnswered $event)
    {
        $emailReceiver = $this->getWhichUserWillGetEmail(EventType::QUESTION_ANSWERED, $event->question->language ?? DEFAULT_LANGUAGE);
        if ($emailReceiver['customer'] && $event->question->customer) {
            $customer = User::findOrFail($event->question->user_id);
            $customer->notify(new NotifyQuestionAnswered($event->question));
        }
    }
}
