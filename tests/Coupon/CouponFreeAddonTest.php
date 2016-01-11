<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\Term;
use Mockery as m;

class CartCouponFreeAddonTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @group coupon
     */
    public function testCartOverAmountCouppon()
    {
        $term = new Term(1);
        $term->trial = -1;
        $term->old = 12;
        $term->price = 100;

        $product = new ProductSharedHosting();
        $product->title = 'Silver';
        $product->billing->addTerm($term);

        $catalog = $this->getCatalog();
        $item = $catalog->getCartItem($product, array(
            'plan' => 'silver',
        ));

        $cart = $this->getCart();
        $cart->add($item);

        $coupons = $this->getCouponsCollection();
        $coupon = $coupons->getCoupon('15_PERCENT_OFF_OVER_30');
        $coupon->calculateDiscount($cart);

        $this->assertEquals(15, $cart->getDiscount());
    }

    /**
     * @group coupon
     */
    public function testCartCouponAmount()
    {
        $term = new Term(1);
        $term->trial = -1;
        $term->old = 12;
        $term->price = 10;

        $product = new ProductSharedHosting();
        $product->id = 21;
        $product->title = 'Silver';
        $product->billing->addTerm($term);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, array(
            'plan' => 'silver',
        ));

        $coupons = $this->getCouponsCollection();
        $coupon = $coupons->getCoupon('MINUS_TWO');

        $cart = $this->getCart();
        $cart->add($item);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(2, $items[0]->getDiscount());
    }

    /**
     * @group coupon
     */
    public function testCartCouponPercent()
    {
        $term = new Term(1);
        $term->trial = -1;
        $term->old = 12;
        $term->price = 10;

        $product = new ProductSharedHosting();
        $product->id = 21;
        $product->title = 'Silver';
        $product->billing->addTerm($term);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, array(
            'plan' => 'silver',
        ));

        $coupons = $this->getCouponsCollection();
        $coupon = $coupons->getCoupon('CYBER');

        $cart = $this->getCart();
        $cart->add($item);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(2.5, $items[0]->getDiscount());
    }

    /**
     * @group coupon
     */
    public function testCartCouponFreeAddon()
    {
        $term = new Term(1);
        $term->trial = -1;
        $term->old = 14.99;
        $term->price = 12.99;

        $product = new ProductDomain();
        $product->id = '.com';
        $product->title = '.com Registration';
        $product->billing->addTerm($term);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, array(
            'domain' => 'example.com',
        ));
        $item_2 = $catalog->getCartItem($product, array(
            'domain' => 'example-2.com',
        ));
        $item_3 = $catalog->getCartItem($product, array(
            'domain' => 'example-3.com',
        ));

        $coupons = $this->getCouponsCollection();
        $coupon = $coupons->getCoupon('BUY_ONE_GET_ONE_FREE');

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_2);
        $cart->add($item_3);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(12.99, $items[0]->getDiscount());
        $this->assertEquals(0, $items[1]->getDiscount());
        $this->assertEquals(0, $items[2]->getDiscount());
    }



    /**
     * @group coupon
     */
    public function testCartCouponFreeAddonAddedFirst()
    {
        $term = new Term(1);
        $term->trial = -1;
        $term->old = 14.99;
        $term->price = 12.99;

        $product = new ProductDomain();
        $product->id = '.com';
        $product->title = '.com Registration';
        $product->billing->addTerm($term);

        $hosting = new ProductSharedHosting();
        $hosting->id = 23;
        $hosting->title = 'Premium';
        $hosting->billing->addTerm($term);


        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, array(
            'domain' => 'example.com',
        ));

        $item_2 = $catalog->getCartItem($hosting);

        $item_3 = $catalog->getCartItem($product, array(
            'domain' => 'example-3.com',
        ));

        $coupons = $this->getCouponsCollection();
        $coupon = $coupons->getCoupon('BUY_HOSTING_GET_DOMAIN_FREE');

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_2);
        $cart->add($item_3);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();

        $this->assertEquals(12.99, $items[0]->getDiscount());
        $this->assertEquals(0, $items[1]->getDiscount());
        $this->assertEquals(0, $items[2]->getDiscount());
    }

    /**
     * @group coupon
     */
    public function testCartCouponBogoHalf()
    {
        $term = new Term(1);
        $term->trial = -1;
        $term->old = 12.00;
        $term->price = 12.00;

        $product = new ProductDomain();
        $product->id = '.com';
        $product->title = '.com Registration';
        $product->billing->addTerm($term);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, array(
            'domain' => 'example.com',
        ));
        $item_2 = $catalog->getCartItem($product, array(
            'domain' => 'example-2.com',
        ));
        $item_3 = $catalog->getCartItem($product, array(
            'domain' => 'example-3.com',
        ));

        $coupons = $this->getCouponsCollection();
        $coupon = $coupons->getCoupon('BUY_ONE_GET_ONE_HALf');

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

    public function getCouponsCollection()
    {
        $json = __DIR__ . '/coupons.json';
        $array = json_decode(file_get_contents($json), true);
        $collection = new CouponCollection();
        $collection->import($array);
        return $collection;
    }

    public function getCatalog()
    {
        $json = __DIR__ . '/../catalog.json';
        $array = json_decode(file_get_contents($json), true);
        $catalog = new Catalog();
        $catalog->import($array);
        return $catalog;
    }

    public function getCart()
    {
        $store = m::mock('Cart\Storage\Store');
        return new Cart('foo', $store);
    }

}