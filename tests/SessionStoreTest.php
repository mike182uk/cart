<?php

use Cart\Storage\SessionStore;

class SessionStoreTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $data = 'bar';

        $store = new SessionStore();
        $store->put('foo', $data);

        $this->assertSame($store->get('foo'), $data);
    }

    public function testPut()
    {
        $data = 'bar';

        $store = new SessionStore();
        $store->put('foo', $data);

        $this->assertSame($_SESSION['foo'], $data);
    }

    public function testFlush()
    {
        $store = new SessionStore();
        $store->put('foo', 'bar');

        $store->flush('foo');

        $this->assertFalse(isset($_SESSION['foo']));
    }
}
