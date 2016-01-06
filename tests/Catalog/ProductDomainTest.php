<?php

use Cart\Catalog\ProductDomain;
use Mockery as m;

class ProductDomainTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testIsArrayable()
    {
        $p = new ProductDomain();
        $p->id = 1;
        $p->title = 'Domain registration';
        $p->description = 'Domain registration';

        $this->assertInternalType('array', $p->toArray());
    }
}