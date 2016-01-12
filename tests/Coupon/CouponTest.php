<?php

use Cart\Cart;
use Cart\Coupon\Coupon;
use Cart\Coupon\CouponCollection;
use Mockery as m;

class CouponTest extends CartTestCase
{
    public function testIsCouponActiveProvider()
    {
        return [
            [
                '2016-01-01',
                '2019-01-20',
                true
            ],

            [
                '2017-01-01',
                '2016-01-20',
                false
            ],

            [
                '2010-01-01',
                '2012-01-20',
                false
            ],
            [
                null,
                '2012-01-20',
                false
            ],

            [
                null,
                '2019-01-20',
                true
            ],
            [
                '2010-01-20',
                null,
                true
            ],
            [
                '2019-01-20',
                null,
                false
            ],

            [
                null,
                null,
                true
            ],

        ];
    }

    /**
     * @dataProvider testIsCouponActiveProvider
     * @group coupon
     */
    public function testIsCouponActive($from, $until, $expected)
    {
        $coupon = new Coupon();
        $coupon->setValidFrom($from);
        $coupon->setValidUntil($until);

        $this->assertInternalType('boolean', $coupon->isActive());
        $this->assertEquals($expected, $coupon->isActive());
    }
}
