<?php

use Cart\SessionStore;

class SessionStoreTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $data = array('bar');

        $store = new SessionStore();
        $store->put('foo', $data);

        $this->assertSame($store->get('foo'), $data);
    }

    public function testPut()
    {
        $data = array('bar');

        $store = new SessionStore();
        $store->put('foo', $data);

        $this->assertSame($_SESSION['foo'], $data);
    }

    public function testFlush()
    {
        $store = new SessionStore();
        $store->put('foo', array('bar'));

        $store->flush('foo');

        $this->assertFalse(isset($_SESSION['foo']));
    }
}
