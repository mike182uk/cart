<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponPercentDiscount implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config = $coupon->getConfig();
        $percent = $config['percent'];

        foreach ($cart as &$item) {
            if (empty($products) || in_array($item->getProductId(), $products)) {
                $discount = $item->getPrice() - $item->getPrice() * (1 - $percent / 100);
                $item->setDiscount($discount);
            }
        }
    }
}
