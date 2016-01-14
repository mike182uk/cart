<?php

namespace Cart\Coupon;

use Cart\Cart;

class CouponAmountDiscount implements CouponInterface
{
    use CouponApplicableTrait;

    public function calculateDiscount(Coupon $coupon, Cart $cart)
    {
        $products = $coupon->getProducts();
        $config = $coupon->getConfig();
        $amount = $config['amount'];

        foreach ($cart as &$item) {
            if (!$this->isApplicable($item, $products)) {
                continue;
            }

            if (empty($products) || array_key_exists($item->getProductId(), $products)) {
                if($amount > $item->getPrice()) {
                    $item->setDiscount($item->getPrice());
                } else {
                    $item->setDiscount($amount);
                }
            }
        }
    }
}
