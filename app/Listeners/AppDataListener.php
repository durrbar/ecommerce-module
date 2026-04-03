<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Listeners;

use Carbon\Carbon;
use Modules\Core\Console\DurrbarVerification;
use Modules\Ecommerce\Events\ProcessUserData;

class AppDataListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly DurrbarVerification $config) {}

    /**
     * Handle the event.
     */
    public function handle(ProcessUserData $event): void
    {
        $lastCheckingTime = $this->config->getLastCheckingTime();
        $lastCheckingTimeDifferenceFromNow = Carbon::parse($lastCheckingTime)->diffInHours(Carbon::now());

        if ($lastCheckingTimeDifferenceFromNow < 12) {
            return;
        }

        $key = $this->config->getPrivateKey();
        $language = request('language', DEFAULT_LANGUAGE);
        $this->config->verify($key)->modifySettingsData($language);
    }
}
