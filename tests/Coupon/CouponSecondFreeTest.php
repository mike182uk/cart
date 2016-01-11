<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CartCouponSecondFreeTest extends CartCouponTestCase
{

    /**
     * @group coupon
     */
    public function testCartCouponSecondFreeMultiple()
    {
        $term        = new Term(1);
        $term->trial = -1;
        $term->old   = 14.99;
        $term->price = 12.99;

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
        $item_4 = $catalog->getCartItem($product, [
            'domain' => 'example-4.com',
        ]);

        $item_5 = $catalog->getCartItem($product, [
            'domain' => 'example-5.com',
        ]);
        $item_6 = $catalog->getCartItem($product, [
            'domain' => 'example-6.com',
        ]);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('BUY_ONE_GET_CLONE');

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_2);
        $cart->add($item_3);
        $cart->add($item_4);
        $cart->add($item_5);
        $cart->add($item_6);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(0, $items[0]->getDiscount());
        $this->assertEquals(12.99, $items[1]->getDiscount());
        $this->assertEquals(0, $items[2]->getDiscount());
        $this->assertEquals(12.99, $items[3]->getDiscount());
        $this->assertEquals(0, $items[4]->getDiscount());
        $this->assertEquals(12.99, $items[5]->getDiscount());
    }



    /**
     * @group coupon
     */
    public function testCartCouponSecondFreeMultipleTerm()
    {
        $term        = new Term(1);
        $term->trial = -1;
        $term->old   = 14.99;
        $term->price = 12.99;

        $term2        = new Term(6);
        $term2->trial = -1;
        $term2->old   = 14.99;
        $term2->price = 12.99;

        $product        = new ProductDomain();
        $product->id    = '.com';
        $product->title = '.com Registration';
        $product->billing->addTerm($term);


        $product2        = new ProductDomain();
        $product2->id    = '.com';
        $product2->title = '.com Registration';
        $product2->billing->addTerm($term2);

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
        $item_4 = $catalog->getCartItem($product2, [
            'domain' => 'example-4.com',
        ]);

        $item_5 = $catalog->getCartItem($product2, [
            'domain' => 'example-5.com',
        ]);
        $item_6 = $catalog->getCartItem($product2, [
            'domain' => 'example-6.com',
        ]);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('BUY_ONE_GET_CLONE');

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_2);
        $cart->add($item_3);
        $cart->add($item_4);
        $cart->add($item_5);
        $cart->add($item_6);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(0, $items[0]->getDiscount());
        $this->assertEquals($term->getTotalPrice(), $items[1]->getDiscount());
        $this->assertEquals(0, $items[2]->getDiscount());
        $this->assertEquals(0, $items[3]->getDiscount());
        $this->assertEquals($term2->getTotalPrice(), $items[4]->getDiscount());
        $this->assertEquals(0, $items[5]->getDiscount());
    }

    /**
     * @group coupon
     */
    public function testCartCouponFreeAddonSingle()
    {
        $term        = new Term(1);
        $term->trial = -1;
        $term->old   = 14.99;
        $term->price = 12.99;

        $product        = new ProductDomain();
        $product->id    = '.com';
        $product->title = '.com Registration';
        $product->billing->addTerm($term);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, [
            'domain' => 'example.com',
        ]);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('BUY_ONE_GET_CLONE');

        $cart = $this->getCart();
        $cart->add($item);

        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(0, $items[0]->getDiscount());

    }

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