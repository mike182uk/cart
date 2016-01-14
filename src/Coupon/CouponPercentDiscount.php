<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponPercentDiscount implements CouponInterface
{
    use CouponApplicableTrait;

    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config = $coupon->getConfig();
        $percent = $config['percent'];

        foreach ($cart as &$item) {
            if (!$this->isApplicable($item, $products)) {
                continue;
            }

            if (empty($products) || array_key_exists($item->getProductId(), $products)) {
                $discount = $item->getPrice() - $item->getPrice() * (1 - $percent / 100);
                $item->setDiscount($discount);
            }
        }
    }
}
