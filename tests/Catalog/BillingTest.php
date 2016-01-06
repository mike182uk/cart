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

    public function testSaveForTerm()
    {
        $term = new Term(1);
        $term->old = 200;
        $term->price = 100;

        $billing = new Billing();
        $billing->addTerm($term);

        $this->assertEquals(100, $billing->getSaveForTerm($term));
    }

    public function testSavePercentForTerm()
    {
        $term = new Term(1);
        $term->old = 200;
        $term->price = 100;

        $billing = new Billing();
        $billing->addTerm($term);

        $this->assertEquals(100, $billing->getSavePercentForTerm($term));
    }
}