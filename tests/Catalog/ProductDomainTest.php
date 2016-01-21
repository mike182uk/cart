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
        $p->setId(1);
        $p->setTitle('Domain registration');
        $p->setDescription('Domain registration');

        $this->assertInternalType('array', $p->toArray());
    }
}
