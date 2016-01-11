<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CouponPercentDiscountTest extends CartCouponTestCase
{

    /**
     * @group coupon
     */
    public function testCartCouponPercent()
    {
        $term        = new Term(1);
        $term->trial = -1;
        $term->old   = 12;
        $term->price = 10;

        $product        = new ProductSharedHosting();
        $product->id    = 21;
        $product->title = 'Silver';
        $product->billing->addTerm($term);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, [
            'plan' => 'silver',
        ]);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('CYBER');

        $cart = $this->getCart();
        $cart->add($item);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(2.5, $items[0]->getDiscount());
    }


}