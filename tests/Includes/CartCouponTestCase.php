<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

abstract class CartCouponTestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function getCouponsCollection()
    {
        $json       = __DIR__ . '/../Coupon/coupons.json';
        $array      = json_decode(file_get_contents($json), true);
        $collection = new CouponCollection();
        $collection->import($array);

        return $collection;
    }

    public function getCatalog()
    {
        $json    = __DIR__ . '/../catalog.json';
        $array   = json_decode(file_get_contents($json), true);
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