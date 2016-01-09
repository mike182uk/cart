<?php

namespace Cart\Coupon;

use Cart\Cart;

interface CouponInterface
{
    public function calculateDiscount(Coupon $coupon, Cart $cart);
}