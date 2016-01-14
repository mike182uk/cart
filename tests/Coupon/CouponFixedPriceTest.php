<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CartCouponFixedBrandTest extends CartTestCase
{

    /**
     * @group coupon
     */
    public function testCartCouponFixedPrice()
    {
        $period = 10;

        $term = new Term($period);
        $term->setOld(12);
        $term->setPrice(10);
        $term->setTrial(1);

        $term2 = new Term($period);
        $term2->setPrice(2);

        $product = new ProductSharedHosting();
        $product->setId(21);
        $product->setTitle('Silver');
        $product->getBilling()->addTerm($term);

        $ssl = new ProductSsl();
        $ssl->setId(22);

        $ssl->getBilling()->addTerm($term2);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, [
            'plan' => 'silver',
        ]);

        $item_ssl = $catalog->getCartItem($ssl, []);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('5_DOLLAR_SALE');

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_ssl);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();

        //price should be 5 * $period because fixed $5 coupon is set
        $this->assertEquals(5 * $period, $items[0]->getPriceWithDiscount());

        //price should not be affected because initial price is lower than $5
        $this->assertEquals(2 * $period, $items[1]->getPriceWithDiscount());
        $this->assertEquals($items[1]->getPrice(), $items[1]->getPriceWithDiscount());
    }
}
