<?php

use Cart\Cart;
use Cart\Catalog\Term;
use Cart\Coupon\Coupon;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\Catalog;
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

    public function testToString()
    {
        $coupon = new Coupon('FRIDAY');
        $this->assertEquals('FRIDAY', $coupon->__toString());
    }

    public function testApplicableProvider()
    {
        return
            [
                [
                    1,
                    [24 => [1, 2]],
                    true //Period is in defined periods for product
                ],

                [
                    1,
                    [25 => [1, 2]],
                    false //Period is in defined periods but it should apply to another product
                ],

                [
                    3,
                    [24 => [1, 2]],
                    false //Period is NOT in defined periods for product
                ],
                [
                    3,
                    [24 => []],
                    true  //No defined periods set, all of them should be valid
                ],
                [
                    3,
                    [24 => []],
                    true  //No defined periods set, all of them should be valid
                ],
                [
                    3,
                    [],
                    true  //No periods and products are set, coupon should apply to everything
                ],

            ];
    }

    /**
     * @dataProvider testApplicableProvider
     * @group coupon
     */
    public function testApplicable($period, $products, $applied)
    {
        $term = new Term($period);
        $term->setPrice(12.00);

        $product = new \Cart\Catalog\Product();
        $product->setId(24);
        $product->getBilling()->addTerm($term);

        $catalog = new Catalog();
        $item    = $catalog->getCartItem($product);

        $cart = $this->getCart();
        $cart->add($item);

        $total = $cart->total();

        $coupon = new Coupon('COUPON');
        $coupon->setProducts($products);
        $coupon->setType('PercentDiscount');
        $coupon->setConfig(['percent' => 50]);
        $coupon->calculateDiscount($cart);

        $this->assertEquals($total != $cart->total(), $applied);
    }
}
