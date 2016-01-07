<?php

use Cart\Catalog\Product;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\ProductSharedHosting;
use Cart\Catalog\ProductVps;
use Cart\Catalog\ProductSsl;
use Cart\Catalog\ProductCpanelHosting;
use Cart\Catalog\Term;
use Cart\Catalog\Catalog;
use Mockery as m;

class CatalogTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testImport()
    {
        $json = __DIR__ . '/../catalog.json';
        $array = json_decode(file_get_contents($json), true);
        $catalog = new Catalog();
        $catalog->import($array);

        //file_put_contents($json, json_encode($catalog, \JSON_PRETTY_PRINT));

        $this->assertEquals($array, $catalog->toArray());
    }

    public function testIsArrayable()
    {
        $catalog = new Catalog();

        $this->assertInternalType('array', $catalog->toArray());
    }

    public function testAddProduct()
    {
        $catalog = new Catalog();
        $catalog->addProduct(new Product());
        $this->assertInternalType('array', $catalog->toArray());
    }

    public function testGetCartItem()
    {
        $term = new Term(1);
        $product = new Product();
        $product->billing->addTerm($term);

        $productDomain = new ProductDomain();
        $productDomain->billing->addTerm($term);

        $productSharedHosting = new ProductSharedHosting();
        $productSharedHosting->billing->addTerm($term);

        $productCpanelHosting = new ProductCpanelHosting();
        $productCpanelHosting->billing->addTerm($term);

        $productVps = new ProductVps();
        $productVps->billing->addTerm($term);

        $productSsl = new ProductSsl();
        $productSsl->billing->addTerm($term);

        $catalog = new Catalog();

        $this->assertInstanceOf('\Cart\CartItem', $catalog->getCartItem($productCpanelHosting));
        $this->assertInstanceOf('\Cart\CartItem', $catalog->getCartItem($productSsl));
        $this->assertInstanceOf('\Cart\CartItemVps', $catalog->getCartItem($productVps));
        $this->assertInstanceOf('\Cart\CartItemSharedHosting', $catalog->getCartItem($productSharedHosting));
        $this->assertInstanceOf('\Cart\CartItemDomain', $catalog->getCartItem($productDomain));
        $this->assertInstanceOf('\Cart\CartItem', $catalog->getCartItem($product));
    }
}