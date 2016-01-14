<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponFixedPrice implements CouponInterface
{
    use CouponApplicableTrait;

    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config   = $coupon->getConfig();
        $price    = $config['price'];

        foreach ($cart as &$item) {
            if (!$this->isApplicable($item, $products)) {
                continue;
            }

            $period = $item->getTerm()->getPeriod();

            $priceForPeriod = $price * $period;
            if ($priceForPeriod < $item->getPrice()) {
                $item->setDiscount($item->getPrice() - $priceForPeriod);
            }
        }
    }
}
