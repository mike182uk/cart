<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponSecondFree implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config   = $coupon->getConfig();
        $addon = $config['addon'];
        $multiple = $config['multiple'];

        $found = false;

        foreach ($cart as &$item) {
            if ($found == false && array_key_exists($item->getProductId(), $products)) {
                $found = true;
                continue;
            }
        }


        if ($found) {
            $i = 0;
            foreach ($cart as &$item) {
                if ($item->getProductId() == $addon) {
                    $i++;
                    if ($i == 2 || ($multiple && $i % 2 == 0)){
                        $item->setDiscount($item->getPrice());
                        if (!$multiple){
                            break;
                        }
                    }
                }
            }
        }

    }

}