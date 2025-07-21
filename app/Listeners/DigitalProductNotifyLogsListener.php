<?php

namespace Modules\Ecommerce\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Ecommerce\Events\DigitalProductUpdateEvent;
use Modules\Ecommerce\Models\NotifyLogs;
use Modules\Ecommerce\Models\Settings;
use Modules\Ecommerce\Notifications\DigitalProductUpdateNotification;
use Modules\Order\Models\Order;
use Modules\User\Models\User;
use Modules\User\Traits\UsersTrait;

class DigitalProductNotifyLogsListener implements ShouldQueue
{
    use UsersTrait;

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(DigitalProductUpdateEvent $event)
    {

        // save notification for vendor
        if (isset($event->product)) {
            $ordered_files = DB::table('ordered_files')
                ->join('digital_files', 'ordered_files.digital_file_id', '=', 'digital_files.id')
                ->when($event->product['product_type'] == 'variable', function ($query): void {
                    $query->join('variation_options', 'digital_files.fileable_id', '=', 'variation_options.id')
                        ->join('products', 'products.id', '=', 'variation_options.product_id');
                })
                ->when($event->product['product_type'] == 'simple', function ($query): void {
                    $query->join('products', 'products.id', '=', 'digital_files.fileable_id');
                })
                ->select(
                    'ordered_files.id as id',
                    'ordered_files.customer_id as customer_id',
                    'ordered_files.purchase_key as purchase_key',
                    'ordered_files.digital_file_id as digital_file_id',
                    'ordered_files.tracking_number as tracking_number'
                )
                ->where('products.id', '=', $event->product->id)
                ->groupBy(
                    'ordered_files.id',
                    'ordered_files.customer_id',
                    'ordered_files.purchase_key',
                    'ordered_files.digital_file_id',
                    'ordered_files.tracking_number'
                )
                ->get();

            // create notify_logs for that ordered files with each purchased customer.
            if (isset($ordered_files)) {
                foreach ($ordered_files as $value) {
                    // if (!NotifyLogs::where('notify_tracker', '=', $event->product->id)->where('receiver', '=', $value->customer_id)->exists()) {
                    NotifyLogs::create([
                        'receiver' => $value->customer_id,
                        'sender' => $event->user->id,
                        'notify_type' => 'product_update',
                        'notify_receiver_type' => 'customer',
                        // 'is_read'           => $event->optional_data['inform_customer'],
                        'is_read' => false,
                        'notify_text' => $event->optional_data['update_message'],
                        'notify_tracker' => $event->product->id,
                    ]);

                    // send email to the customers
                    $customer = User::where('id', '=', $value->customer_id)->first();
                    $customer->notify(
                        new DigitalProductUpdateNotification($customer, $event->product, $event->optional_data)
                    );
                    // }
                }
            }
        }
    }

    /**
     * Determine whether the listener should be queued.
     */
    // public function shouldQueue(DigitalProductUpdateEvent $event): bool
    // {
    //     // $settings = Settings::getData();
    //     // $enableDigitalProductEmail = $settings['options']['enableEmailForDigitalProduct'];

    //     // return $event->order->subtotal >= 5000;

    //     // return true;

    //     // try {
    //     //     $settings = Settings::first();
    //     //     $enableDigitalProductEmail = false;

    //     //     if (isset($settings['options']['enableEmailForDigitalProduct'])) {
    //     //         if ($settings['options']['enableEmailForDigitalProduct'] === true) {
    //     //             $enableDigitalProductEmail = true;
    //     //         }
    //     //     }
    //     //     return $enableDigitalProductEmail;
    //     // } catch (DurrbarException $th) {
    //     //     throw new DurrbarException(SOMETHING_WENT_WRONG, $th->getMessage());
    //     // }
    // }
}
