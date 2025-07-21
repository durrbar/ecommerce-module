<?php

namespace Modules\Ecommerce\Listeners;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Notification;
use Modules\Ecommerce\Events\Maintenance;
use Modules\Ecommerce\Notifications\MaintenanceReminder;
use Modules\Role\Enums\Permission;
use Modules\Settings\Models\Settings;
use Modules\User\Models\User;

class MaintenanceNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(Maintenance $event)
    {
        $language = $event->language;
        $settings = Settings::getData($language);
        $shouldSendEmail = $this->shouldSendEmail($settings);

        if (! $shouldSendEmail) {
            return;
        }

        $admins = User::permission(Permission::SUPER_ADMIN)->pluck('id')->toArray();
        $users = User::permission(Permission::STORE_OWNER)->whereNotIN('id', $admins)->get();
        if ($users) {
            foreach ($users as $user) {
                Notification::route('mail', [
                    $user->email,
                ])->notify(new MaintenanceReminder($settings));
            }
        }
    }

    public function shouldSendEmail(Settings $settings): bool
    {
        $shouldSendEmail = false;
        try {
            $isUnderMaintenance = $settings->options['isUnderMaintenance'] ?? false;
            $currentTime = now();
            $startTime = Carbon::parse($settings->options['maintenance']['start']);

            $shouldSendEmail = $isUnderMaintenance && ($currentTime < $startTime);
        } catch (Exception $th) {
        }

        return $shouldSendEmail;
    }
}
