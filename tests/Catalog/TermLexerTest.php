<?php

use Cart\Catalog\TermLexer;
use Mockery as m;

class TermLexerTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException \Exception
     */
    public function testException()
    {
        new TermLexer('1');
    }

    /**
     * @expectedException \Exception
     */
    public function testException2()
    {
        new TermLexer('1Z');
    }

    public function testDays()
    {
        $p = new TermLexer('500D');
        $this->assertEquals('500 day(s)', $p->getTitle());
    }

    public function testOneMonth()
    {
        $p = new TermLexer('1M');

        $this->assertEquals('M', $p->getUnit());
        $this->assertEquals(1, $p->getQty());
        $this->assertEquals('1M', $p->getCode());
        $this->assertEquals('1 month(s)', $p->getTitle());
        $this->assertEquals(30, $p->getDays());
        $this->assertEquals(strtotime('+1 month'), $p->getExpirationTime());
    }
}