<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponFixedPrice implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $config = $coupon->getConfig();
        $price  = $config['price'];

        foreach ($cart as &$item) {
            $period         = $item->getTerm()->getPeriod();
            $priceForPeriod = $price * $period;
            if ($priceForPeriod < $item->getPrice()) {
                $item->setDiscount($item->getPrice() - $priceForPeriod);
            }
        }
    }
}
