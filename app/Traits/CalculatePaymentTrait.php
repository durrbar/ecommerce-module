<?php

namespace Modules\Ecommerce\Traits;

use Modules\Core\Exceptions\DurrbarException;
use Modules\Coupon\Enums\CouponType;
use Modules\Ecommerce\Models\Product;
use Modules\Ecommerce\Models\Variation;

trait CalculatePaymentTrait
{
    use WalletsTrait;

    public function calculateSubtotal($cartItems)
    {
        if (! is_array($cartItems)) {
            throw new DurrbarException(CART_ITEMS_NOT_FOUND);
        }
        $subtotal = 0;
        try {
            foreach ($cartItems as $item) {
                if (isset($item['variation_option_id'])) {
                    $variation = Variation::findOrFail($item['variation_option_id']);
                    $subtotal += $this->calculateEachItemTotal($variation, $item['order_quantity']);
                } else {
                    $product = Product::findOrFail($item['product_id']);
                    $subtotal += $this->calculateEachItemTotal($product, $item['order_quantity']);
                }
            }

            return $subtotal;
        } catch (\Throwable $th) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    public function calculateDiscount($coupon, $subtotal)
    {
        if ($coupon->id) {
            if ($coupon->type === CouponType::PERCENTAGE_COUPON) {
                return $subtotal * ($coupon->amount / 100);
            } elseif ($coupon->type === CouponType::FIXED_COUPON) {
                return $coupon->amount;
            }
        } else {
            return 0;
        }
    }

    public function calculateEachItemTotal($item, $quantity)
    {
        $total = 0;
        if ($item->sale_price) {
            $total += $item->sale_price * $quantity;
        } else {
            $total += $item->price * $quantity;
        }

        return $total;
    }

    public function getUserWalletAmount($user)
    {
        $amount = 0;
        $wallet = $user->wallet;
        if (isset($wallet->available_points)) {
            $amount = $this->walletPointsToCurrency($wallet->available_points);
        }

        return $amount;
    }
}
