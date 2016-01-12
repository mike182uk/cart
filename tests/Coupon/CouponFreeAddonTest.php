<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CartCouponFreeAddonTest extends CartTestCase
{
    /**
     * @group coupon
     */
    public function testCartCouponFreeAddonAddedFirst()
    {
        $term        = new Term(1);
        $term->trial = -1;
        $term->old   = 14.99;
        $term->price = 12.99;


        $term_ssl        = new Term(1);
        $term_ssl->trial = 15;
        $term_ssl->old   = 0;
        $term_ssl->price = 49.00;

        $product        = new ProductDomain();
        $product->id    = '.com';
        $product->title = '.com Registration';
        $product->billing->addTerm($term);

        $hosting        = new ProductSharedHosting();
        $hosting->id    = 24;
        $hosting->title = 'Premium';
        $hosting->billing->addTerm($term);


        $ssl        = new ProductSsl();
        $ssl->id    = 21;
        $ssl->title = 'SSL Certificate';
        $ssl->billing->addTerm($term_ssl);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, [
            'domain' => 'example.com',
        ]);

        $item_hosting = $catalog->getCartItem($hosting);

        $item_3 = $catalog->getCartItem($product, [
            'domain' => 'example-3.com',
        ]);

        $item_ssl = $catalog->getCartItem($ssl);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('BUY_HOSTING_GET_DOMAIN_AND_SSL_FREE');

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_hosting);
        $cart->add($item_3);
        $cart->add($item_ssl);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();

        $this->assertEquals(12.99, $items[0]->getDiscount());
        $this->assertEquals(0, $items[1]->getDiscount());
        $this->assertEquals(0, $items[2]->getDiscount());
        $this->assertEquals(15, $items[3]->getDiscount());
    }
}