<?php

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

        $item = $catalog->getCartItem($product, null, [
            'plan' => 'silver',
        ]);

        $item_ssl = $catalog->getCartItem($ssl, null, []);

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

    /**
     * @group coupon
     */
    public function testFixedPriceForTerm()
    {
        $term = new Term(12);
        $term->setPrice(6);

        $term_2 = new Term(36);
        $term_2->setPrice(5);

        $product = new ProductSharedHosting();
        $product->setId(21);
        $product->setTitle('Silver');
        $product->getBilling()->addTerm($term);
        $product->getBilling()->addTerm($term_2);

        $catalog = $this->getCatalog();

        $item_1 = $catalog->getCartItem($product, $term, [
            'plan' => 'silver',
        ]);

        $item_2 = $catalog->getCartItem($product, $term_2, [
            'plan' => 'gold',
        ]);

        $cart = $this->getCart();
        $cart->add($item_1);
        $cart->add($item_2);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('1_DOLLAR_HOSTING');
        $coupon->calculateDiscount($cart);

        $items = $cart->all();

        $this->assertEquals(2, $cart->totalItems());

        // only 12 months term has 1 dollar price
        $this->assertEquals(1 * $term->getPeriod(), $items[0]->getPriceWithDiscount());

        //other term does not have discount
        $this->assertEquals(5 * $term_2->getPeriod(), $items[1]->getPriceWithDiscount(), 'other item in cart failed');
    }
}
