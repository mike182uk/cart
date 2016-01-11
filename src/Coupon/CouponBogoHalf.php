<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponBogoHalf implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config = $coupon->getConfig();
        $addon = $config['addon'];

        $found = false;
        foreach ($cart as &$item) {
            if ($found == false && array_key_exists($item->getProductId(), $products)) {
                $found = true;
                continue;
            }
            if ($found && $item->getProductId() == $addon) {
                $item->setDiscount($item->getPrice() / 2);
                break;
            }
        }
    }

}