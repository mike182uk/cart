<?php

use Cart\Catalog\Product;
use Mockery as m;

class ProductTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testIsArrayable()
    {
        $p = new Product();
        $p->setId(1);
        $p->setTitle('Domain registration');
        $p->setDescription('Domain registration');

        $this->assertInternalType('array', $p->toArray());
    }
}