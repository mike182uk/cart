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
        $p->id = 1;
        $p->title = 'Domain registration';
        $p->description = 'Domain registration';

        $this->assertInternalType('array', $p->toArray());
    }
}