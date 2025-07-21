<?php

namespace Modules\Ecommerce\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Vendor\Models\Balance;
use Modules\Vendor\Models\Shop;

class AdminCommissionRateUpdate extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Shop $shop;

    public Balance $balance;

    /**
     * Create a new event instance.
     */
    public function __construct(Shop $shop, Balance $balance)
    {
        $this->shop = $shop;
        $this->balance = $balance;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.order.admin-commission-rate-update');
    }
}
