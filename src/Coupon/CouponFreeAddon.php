<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponFreeAddon implements CouponInterface
{
    use CouponApplicableTrait;

    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config   = $coupon->getConfig();
        $addons   = $config['addon'];
        $found    = false;

        foreach ($cart as $item) {
            if (!$this->isApplicable($item, $products)) {
                continue;
            }

            if ($found == false && array_key_exists($item->getProductId(), $products)) {
                $found = true;
                break;
            }
        }

        if ($found) {
            foreach ($cart as &$item) {
                if (array_key_exists($item->getProductId(), $addons) && (empty($addons[$item->getProductId()]) || in_array($item->getTerm()->getPeriod(), $addons[$item->getProductId()]))) {
                    $item->setDiscount($item->getPrice());
                    unset($addons[$item->getProductId()]);
                }
            }
        }
    }
}
