<?php

namespace Cart;

use Cart\Storage\CookieStore;
use PHPUnit\Framework\TestCase;

/**
 * @TODO: write testings for put, get and flush
 */
class CookieStoreTest extends TestCase
{
    public function testEncodeAndDecode()
    {
        $data = 'Foo';
        $store = new CookieStore();
        $encodedData = $store->encode($data);

        $this->assertSame($data, $store->decode($encodedData));
    }
}
