<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponBogoHalf implements CouponInterface
{
    use CouponApplicableTrait;

    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config = $coupon->getConfig();
        $addon = $config['addon'];

        $found = false;
        foreach ($cart as $item) {
            if (!$this->isApplicable($item, $products)) {
                continue;
            }

            if ($found == false && array_key_exists($item->getProductId(), $products)) {
                $found = $item->getId();
                break;
            }
        }

        if ($found) {
            foreach ($cart as &$item) {
                if ($item->getId() != $found && in_array($item->getProductId(), $addon)) {
                    $item->setDiscount($item->getPrice() / 2);
                    break;
                }
            }
        }
    }
}
