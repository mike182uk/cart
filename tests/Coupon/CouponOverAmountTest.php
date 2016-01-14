<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CouponOverAmountTest extends CartTestCase
{

    /**
     * @group coupon
     */
    public function testCartOverAmountCouppon()
    {
        $term        = new Term(1);
        $term->setPrice(100);

        $product        = new ProductSharedHosting();
        $product->setTitle('Silver');
        $product->getBilling()->addTerm($term);

        $catalog = $this->getCatalog();
        $item    = $catalog->getCartItem($product, null, [
            'plan' => 'silver',
        ]);

        $cart = $this->getCart();
        $cart->add($item);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('15_PERCENT_OFF_OVER_30');
        $coupon->calculateDiscount($cart);

        $this->assertEquals(15, $cart->getDiscount());
    }
}
