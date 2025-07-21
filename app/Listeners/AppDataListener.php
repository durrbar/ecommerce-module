<?php

namespace Modules\Ecommerce\Listeners;

use Carbon\Carbon;
use Modules\Core\Console\DurrbarVerification;
use Modules\Ecommerce\Events\ProcessUserData;

class AppDataListener
{
    private $appData;

    private $config;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DurrbarVerification $config)
    {
        $this->config = $config;
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(ProcessUserData $event)
    {
        $last_checking_time = $this->config->getLastCheckingTime();
        $lastCheckingTimeDifferenceFromNow = Carbon::parse($last_checking_time)->diffInHours(Carbon::now());
        if ($lastCheckingTimeDifferenceFromNow < 12) {
            return;
        }
        $key = $this->config->getPrivateKey();
        $language = isset(request()['language']) ? request()['language'] : DEFAULT_LANGUAGE;
        $this->config->verify($key)->modifySettingsData($language);
    }
}
