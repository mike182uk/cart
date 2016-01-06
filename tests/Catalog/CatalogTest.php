<?php

use Cart\Catalog\Product;
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
}