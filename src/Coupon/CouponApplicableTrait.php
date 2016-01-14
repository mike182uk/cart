<?php

namespace Cart\Coupon;

use Cart\CartItem;

trait CouponApplicableTrait
{
    public function isApplicable(CartItem $item, array $products = [])
    {
        $period = $item->getTerm()->getPeriod();
        if (!empty($products)) {
            if (!array_key_exists($item->getProductId(), $products)) {
                return false;
            }
            $couponProductPeriods = $products[$item->getProductId()];
            if (!empty($couponProductPeriods) && !in_array($period, $couponProductPeriods)) {
                return false;
            }
        }

        return true;
    }
}
