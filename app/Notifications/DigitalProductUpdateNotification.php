<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Ecommerce\Models\Product;
use Modules\User\Models\User;

class DigitalProductUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $user;

    protected Product $product;

    protected mixed $optional_message;

    /**
     * Create a new notification instance.
     *
     */
    public function __construct(User $user, Product $product, mixed $optional_message = null)
    {
        $this->user = $user;
        $this->product = $product;
        $this->optional_message = $optional_message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('New product update is available.')
            ->markdown(
                'products.digital_product.update',
                [
                    'user' => $this->user,
                    'product' => $this->product,
                    'url' => config('shop.shop_url').'/products/'.$this->product->slug,
                    'optional_message' => $this->optional_message,
                ]
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }
}
