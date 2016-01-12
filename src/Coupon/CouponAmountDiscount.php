<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponAmountDiscount implements CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config = $coupon->getConfig();
        $amount = $config['amount'];

        foreach ($cart as &$item) {
            if (empty($products) || in_array($item->getProductId(), $products)) {
                if($amount > $item->getPrice()) {
                    $item->setDiscount($item->getPrice());
                } else {
                    $item->setDiscount($amount);
                }
            }
        }
    }
}
