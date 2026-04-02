<?php

declare(strict_types=1);

namespace Modules\Ecommerce\Traits;

use Modules\Ecommerce\Models\Wallet;
use Modules\Settings\Models\Settings;
use Throwable;

trait WalletsTrait
{
    /**
     * Converts wallet points to currency
     */
    public function walletPointsToCurrency(int|float $points): float
    {
        $currencyToWalletRatio = $this->currencyToWalletRatio();

        $currency = $points / $currencyToWalletRatio;

        return round($currency, 2);
    }

    /**
     * Converts wallet points to currency
     */
    public function giveSignupPointsToCustomer(int|string $customer_id): void
    {
        try {
            $settings = Settings::getData();
            $signupPoints = $settings['options']['signupPoints'];
        } catch (Throwable $th) {
            $signupPoints = 0;
        }

        $wallet = Wallet::firstOrCreate(['customer_id' => $customer_id]);
        $wallet->total_points += $signupPoints;
        $wallet->available_points += $signupPoints;
        $wallet->save();
    }

    /**
     * Convert currency to wallet points
     */
    private function currencyToWalletPoints(int|float $currency): int
    {
        $currencyToWalletRatio = $this->currencyToWalletRatio();

        $points = $currency * $currencyToWalletRatio;

        return (int) $points;
    }

    private function currencyToWalletRatio(): int|float
    {
        try {
            $settings = Settings::getData();
            $currencyToWalletRatio = $settings['options']['currencyToWalletRatio'];
        } catch (Throwable $th) {
            $currencyToWalletRatio = 1;
        }

        return $currencyToWalletRatio === 0 ? 1 : $currencyToWalletRatio;
    }
}
