<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\User\Models\User;
use Modules\Vendor\Models\StoreNotice;

class TestPusherEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly StoreNotice $storeNotice, public User $user) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        $eventChannels = [];
        if (isset($this->storeNotice->users)) {
            foreach ($this->storeNotice->users as $user) {
                $eventChannels[] = new PrivateChannel('store_notice.created.'.$user->id);
            }
        }

        return $eventChannels;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'store-notice' => $this->storeNotice,
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'test.pusher.event';
    }
}
