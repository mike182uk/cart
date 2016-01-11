<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponSecondFree implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config   = $coupon->getConfig();

        $found = false;

        $eligibleItems = [];

        foreach ($cart as &$item) {
            if (array_key_exists($item->getProductId(), $products)) {
                $found                = true;
                $hash                 = $this->getHash($item);
                $eligibleItems[$hash] = 0;
            }
        }

        if ($found) {
            foreach ($cart as &$item) {
                $hash2 = $this->getHash($item);
                if (array_key_exists($hash2, $eligibleItems)) {
                    $eligibleItems[$hash2]++;
                    if ($eligibleItems[$hash2] % 2 == 0) {
                        $item->setDiscount($item->getTerm()->getTotalPrice());
                    }
                }
            }
        }
    }

    private function getHash($item)
    {
        return md5($item->getProductId() . "+" . $item->getTerm()->getPeriod());
    }

}