<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CouponBogoHalfTest extends CartCouponTestCase
{

    /**
     * @group coupon
     */
    public function testCartCouponBogoHalf()
    {
        $term        = new Term(1);
        $term->trial = -1;
        $term->old   = 12.00;
        $term->price = 12.00;

        $product        = new ProductDomain();
        $product->id    = '.com';
        $product->title = '.com Registration';
        $product->billing->addTerm($term);

        $catalog = $this->getCatalog();

        $item   = $catalog->getCartItem($product, [
            'domain' => 'example.com',
        ]);
        $item_2 = $catalog->getCartItem($product, [
            'domain' => 'example-2.com',
        ]);
        $item_3 = $catalog->getCartItem($product, [
            'domain' => 'example-3.com',
        ]);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('BUY_ONE_GET_ONE_HALF');

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_2);
        $cart->add($item_3);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(0, $items[0]->getDiscount());
        $this->assertEquals(6, $items[1]->getDiscount());
        $this->assertEquals(0, $items[2]->getDiscount());
    }

}