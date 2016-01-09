<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponFreeAddon implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config = $coupon->getConfig();
        $addon = $config['addon'];

        $found = false;
        foreach ($cart as &$item) {
            if ($found == false && in_array($item->getProductId(), $products)) {
                $found = true;
                continue;
            }
            if ($found && $item->getProductId() == $addon) {
                $item->setDiscount($item->getPrice());
                break;
            }
        }
    }

}