<?php

use Cart\Cart;
use Cart\Coupon\CouponCollection;
use Cart\Catalog\Catalog;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\Term;
use Mockery as m;

class CartCouponFreeAddonTest extends CartTestCase
{
    public function testCartCouponFreeAddonAddedFirstProvicer()
    {
        return [
            [1, 2, true, false],
            [2, 2, false, false],
            [2, 1, false, true],
            [1, 1, true, true],
        ];
    }

    /**
     * @dataProvider testCartCouponFreeAddonAddedFirstProvicer
     * @group coupon
     */
    public function testCartCouponFreeAddonAddedFirst($termDomain, $termSSL, $isDomainFree, $isSSLFree)
    {
        $term = new Term($termDomain);
        $term->setOld(14.99);
        $term->setPrice(12.99);


        $term_ssl = new Term($termSSL);
        $term_ssl->setTrial(15);
        $term_ssl->setOld(0);
        $term_ssl->setPrice(49.00);

        $product = new ProductDomain();
        $product->setId('.com');
        $product->setTitle('.com Registration');
        $product->getBilling()->addTerm($term);

        $hosting = new ProductSharedHosting();
        $hosting->setId(24);
        $hosting->setTitle('Premium');
        $hosting->getBilling()->addTerm($term);


        $ssl = new ProductSsl();
        $ssl->setId(21);
        $ssl->setTitle('SSL Certificate');
        $ssl->getBilling()->addTerm($term_ssl);

        $catalog = $this->getCatalog();

        $item = $catalog->getCartItem($product, [
            'domain' => 'example.com',
        ]);

        $item_hosting = $catalog->getCartItem($hosting);

        $item_3 = $catalog->getCartItem($product, [
            'domain' => 'example-3.com',
        ]);

        $item_ssl = $catalog->getCartItem($ssl);

        $coupons = $this->getCouponsCollection();
        $coupon  = $coupons->getCoupon('BUY_HOSTING_GET_DOMAIN_AND_SSL_FREE');

        $cart = $this->getCart();
        $cart->add($item);
        $cart->add($item_hosting);
        $cart->add($item_3);
        $cart->add($item_ssl);
        $coupon->calculateDiscount($cart);

        $items = $cart->all();

        $expectedDomainDiscount = $isDomainFree ? $items[0]->getPrice() : 0;
        $expectedSSLDiscount    = $isSSLFree ? $items[3]->getPrice() : 0;

        $this->assertEquals($expectedDomainDiscount, $items[0]->getDiscount());
        $this->assertEquals(0, $items[1]->getDiscount());
        $this->assertEquals(0, $items[2]->getDiscount());
        $this->assertEquals($expectedSSLDiscount, $items[3]->getDiscount());
    }
}
