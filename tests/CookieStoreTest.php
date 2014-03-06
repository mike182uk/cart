<?php

use Cart\Storage\CookieStore;

/**
 * @TODO: write testings for put, get and flush
 */
class CookieStoreTest extends PHPUnit_Framework_TestCase
{
    public function testEncodeAndDecode()
    {
        $data = array('bar');
        $store = new CookieStore();
        $encodedData = $store->encode($data);

        $this->assertSame($data, $store->decode($encodedData));
    }
}
