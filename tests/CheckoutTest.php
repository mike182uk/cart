<?php

use Cart\Checkout;
use Cart\Cart;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\Term;
use Cart\Catalog\Catalog;
use Cart\Coupon\Coupon;
use Mockery as m;

class CheckoutTest extends CartTestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testSimpleCheckout()
    {
        $cart = $this->getCartWithItem();
        $checkout = new Checkout($cart);

        $this->assertEquals(12.18, $checkout->getTotal());
        $this->assertEquals(0.18, $checkout->getTotalIcannFees());
        $this->assertEquals(0, $checkout->getTotalTaxes());
        $this->assertEquals(0, $checkout->getTotalSavings());
    }

    public function testCouponCheckout()
    {
        $coupons = $this->getCouponsCollection();
        $coupon = $coupons->getCoupon('50_PERCENT_DISCOUNT');

        $cart = $this->getCartWithItem();
        $checkout = new Checkout($cart);
        $checkout->setCoupon($coupon);

        $this->assertEquals(6.18, $checkout->getTotal());
        $this->assertEquals(0.18, $checkout->getTotalIcannFees());
        $this->assertEquals(0, $checkout->getTotalTaxes());
        $this->assertEquals(6, $checkout->getTotalSavings());
    }

    public function getCartWithItem()
    {
        $term = new Term(1);
        $term->setPrice(12.00);

        $product = new ProductDomain();
        $product->setId('.com');
        $product->setTitle('.com Registration');
        $product->getBilling()->addTerm($term);

        $catalog = new Catalog();
        $item = $catalog->getCartItem($product);

        $cart = $this->getCart();
        $cart->add($item);

        return $cart;
    }
}
