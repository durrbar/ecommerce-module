<?php

namespace Modules\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Ecommerce\Events\MessageSent;
use Modules\Ecommerce\Models\NotifyLogs;
use Modules\User\Traits\UsersTrait;
use Modules\Vendor\Models\Shop;

class StoredMessagedNotifyLogsListener implements ShouldQueue
{
    use UsersTrait;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(MessageSent $event)
    {
        switch ($event->type) {
            case 'shop':
                // save notification for vendor
                $shop_owner = Shop::findOrFail($event->conversation->shop_id);
                NotifyLogs::create([
                    'receiver' => $shop_owner->owner_id,
                    'sender' => $event->user->id,
                    'notify_type' => 'message',
                    'notify_receiver_type' => 'vendor',
                    'is_read' => false,
                    'notify_text' => mb_substr($event->message->body, 0, 15).'...',
                    'notify_tracker' => $event->conversation->id,
                ]);

                break;

            case 'user':
                // save notification for admin
                $admins = $this->getAdminUsers();
                if (isset($admins)) {
                    foreach ($admins as $admin) {
                        NotifyLogs::create([
                            'receiver' => $admin->id,
                            'sender' => $event->user->id,
                            'notify_type' => 'message',
                            'notify_receiver_type' => 'admin',
                            'is_read' => false,
                            'notify_text' => mb_substr($event->message->body, 0, 15).'...',
                            'notify_tracker' => $event->conversation->id,
                        ]);
                    }
                }
                break;
        }
    }
}
