<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponFixedPrice implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config = $coupon->getConfig();
        $price = $config['price'];

        foreach ($cart as &$item) {
            $period = $item->getTerm()->getPeriod();
            if (!empty($products)) {
                $couponProductPeriods = $products[$item->getProductId()];
                if (!in_array($period, $couponProductPeriods)) {
                    continue;
                }
            }

            $priceForPeriod = $price * $period;
            if ($priceForPeriod < $item->getPrice()) {
                $item->setDiscount($item->getPrice() - $priceForPeriod);
            }
        }
    }
}
