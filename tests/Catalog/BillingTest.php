<?php

use Cart\Catalog\Billing;
use Cart\Catalog\Term;
use Mockery as m;

class BillingTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCount()
    {
        $billing = new Billing();
        $this->assertCount(0, $billing->terms);
    }
}
