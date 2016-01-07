<?php

use Cart\Coupon\Coupon;
use Cart\Coupon\CouponCollection;
use Mockery as m;

class CouponCollectionTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testImport()
    {
        $json = __DIR__ . '/coupons.json';
        $array = json_decode(file_get_contents($json), true);
        $collection = new CouponCollection();
        $collection->import($array);

        //file_put_contents($json, json_encode($collection, \JSON_PRETTY_PRINT));

        $this->assertEquals($array, $collection->toArray());
        $this->assertInternalType('array', $collection->toArray());
    }

    public function testIsArrayable()
    {
        $collection = new CouponCollection();
        $this->assertInternalType('array', $collection->toArray());
    }

    public function testAddCoupon()
    {
        $collection = new CouponCollection();
        $collection->addCoupon(new Coupon());
        $this->assertInternalType('array', $collection->toArray());
    }

    public function testGetCoupon()
    {
        $collection = new CouponCollection();

        $coupon = new Coupon();
        $coupon->code = 'BLACK';
        $collection->addCoupon($coupon);

        $coupon = new Coupon();
        $coupon->code = 'CYBER';
        $collection->addCoupon($coupon);

        $coupon = new Coupon();
        $coupon->code = 'HANUKA';
        $collection->addCoupon($coupon);

        $this->assertInstanceOf('\Cart\Coupon\Coupon', $collection->getCoupon('BLACK'));
        $this->assertInstanceOf('\Cart\Coupon\Coupon', $collection->getCoupon('CYBER'));
        $this->assertInstanceOf('\Cart\Coupon\Coupon', $collection->getCoupon('HANUKA'));
    }
}