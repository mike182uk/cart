<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponFreeAddon implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config   = $coupon->getConfig();
        $addons   = $config['addon'];

        $found = false;

        foreach ($cart as &$item) {
            if ($found == false && array_key_exists($item->getProductId(), $products)) {
                $found = true;
                break;
            }
        }

        if ($found) {
            foreach ($cart as &$item) {
                if (in_array($item->getProductId(), $addons)) {
                    $item->setDiscount($item->getPrice());

                    if (($key = array_search($item->getProductId(), $addons)) !== false) {
                        unset($addons[$key]);
                    }

                }
            }
        }
    }
}