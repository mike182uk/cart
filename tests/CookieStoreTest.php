<?php

use Cart\Storage\CookieStore;

/**
 * @TODO: write testings for put, get and flush
 */
class CookieStoreTest extends PHPUnit_Framework_TestCase
{
    public function testEncodeAndDecode()
    {
        $data = 'Foo';
        $store = new CookieStore();
        $encodedData = $store->encode($data);

        $this->assertSame($data, $store->decode($encodedData));
    }
}
