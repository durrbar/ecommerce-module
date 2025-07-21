<?php

namespace Modules\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Models\NotifyLogs;
use Modules\User\Traits\UsersTrait;
use Modules\Vendor\Events\StoreNoticeEvent;

class StoredStoreNoticeNotifyLogsListener implements ShouldQueue
{
    use UsersTrait;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(StoreNoticeEvent $event)
    {
        // save notification for vendor
        if (isset($event->storeNotice->users)) {
            foreach ($event->storeNotice->users as $user) {
                NotifyLogs::create([
                    'receiver' => $user->id,
                    'sender' => $event->user->id,
                    'notify_type' => 'store_notice',
                    'notify_receiver_type' => 'vendor',
                    'is_read' => false,
                    'notify_text' => mb_substr($event->storeNotice->notice, 0, 15).'...',
                    'notify_tracker' => $event->storeNotice->id,
                ]);
            }
        }
    }
}
