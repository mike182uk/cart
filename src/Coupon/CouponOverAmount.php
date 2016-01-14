<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponOverAmount implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $config = $coupon->getConfig();
        $over = $config['over'];
        $percent = $config['percent'];
        $total = $cart->totalExcludingTax();
        if ($total > $over) {
            $discount = $total - $total * (1 - $percent / 100);
            $cart->setDiscount($discount);
        }
    }
}
