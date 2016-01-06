<?php

use Cart\Catalog\Product;
use Cart\Catalog\ProductDomain;
use Cart\Catalog\Term;
use Cart\Catalog\Catalog;
use Mockery as m;

class CatalogTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
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

        $catalog = new Catalog();
        $catalog->addProduct($product);
        $catalog->addProduct($productDomain);

        $this->assertInstanceOf('\Cart\CartItemDomain', $catalog->getCartItem($productDomain));
        $this->assertInstanceOf('\Cart\CartItem', $catalog->getCartItem($product));
    }
}