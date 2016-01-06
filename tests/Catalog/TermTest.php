<?php

use Cart\Catalog\Term;
use Mockery as m;

class TermTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testString()
    {
        $term = new Term('1');
        $this->assertEquals(1, $term->period);
    }

}