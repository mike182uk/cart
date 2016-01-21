<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CouponBogoHalfTest extends CartTestCase
{

    /**
     * @group coupon
     */
    public function testCartCouponBogoHalf()
    {
        $term = new Term(1);
        $term->setOld(12.00);
        $term->setPrice(12.00);

        $product = new ProductDomain();
        $product->setId('.com');
        $product->setTitle('.com Registration');
        $product->getBilling()->addTerm($term);

        $catalog = $this->getCatalog();
        $catalog->addProduct($product);

        $item = $catalog->getCartItem($product, null, [
            'domain' => 'example.com',
        ]);

        $item_2 = $catalog->getCartItem($product, null, [
            'domain' => 'example-2.com',
        ]);
        $item_3 = $catalog->getCartItem($product, null, [
            'domain' => 'example-3.com',
        ]);

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_2);
        $cart->add($item_3);

        $coupons = $this->getCouponsCollection();
        $coupon = $coupons->getCoupon('BUY_ONE_GET_ONE_HALF');
        $coupon->calculateDiscount($cart);

        $items = $cart->all();
        $this->assertEquals(0, $items[0]->getDiscount());
        $this->assertEquals(6, $items[1]->getDiscount());
        $this->assertEquals(0, $items[2]->getDiscount());
    }
}
