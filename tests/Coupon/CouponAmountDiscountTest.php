<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CartCouponAmountDiscountTest extends CartTestCase
{

    /**
     * @group coupon
     */
    public function testCartCouponAmount()
    {
        $term        = new Term(1);
        $term->setOld(12);
        $term->setPrice(10);

        $product        = new ProductSharedHosting();
        $product->setId(21);
        $product->setTitle('Silver');
        $product->getBilling()->addTerm($term);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, [
            'plan' => 'silver',
        ]);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('MINUS_TWO');

        $cart = $this->getCart();
        $cart->add($item);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(2, $items[0]->getDiscount());
    }
}
