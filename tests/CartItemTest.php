<?php

use Cart\CartItem;

class CartItemTest extends CartTestCase
{
    public function testIsArrayable()
    {
        $item = new CartItem();

        $this->assertInstanceOf('Cart\Arrayable', $item);

        $itemArr = $item->toArray();

        $this->assertTrue(is_array($itemArr));
        $this->assertArrayHasKey('id', $itemArr);
        $this->assertArrayHasKey('data', $itemArr);
        $this->assertTrue(is_array($itemArr['data']));
    }

    public function testSetAndGetData()
    {
        $item = new CartItem();

        $item->name = 'foo';
        $this->assertSame($item->get('name'), 'foo');
        $this->assertSame($item['name'], 'foo');
        $this->assertSame($item->name, 'foo');

        $item['name'] = 'bar';
        $this->assertSame($item->get('name'), 'bar');
        $this->assertSame($item->name, 'bar');
        $this->assertSame($item['name'], 'bar');

        $item->set('name', 'baz');
        $this->assertSame($item->get('name'), 'baz');
        $this->assertSame($item['name'], 'baz');
        $this->assertSame($item->name, 'baz');
    }

    public function testIssetAndUnsetData()
    {
        $item = new CartItem([
                                 'name'   => 'foo',
                                 'weight' => '10kg',
                             ]);

        unset($item['name']);

        $this->assertFalse(isset($item['name']));

        unset($item->weight);

        $this->assertFalse(isset($item->weight));
    }

    public function testConstructorSetsData()
    {
        $itemData = [
            'name'     => 'foo',
            'price'    => 10.00,
            'tax'      => 1.00,
            'quantity' => 5,
        ];

        $item = new CartItem($itemData);

        $this->assertTrue($item->name == 'foo');
        $this->assertTrue($item->price === 10.00);
        $this->assertTrue($item->tax === 1.00);
        $this->assertTrue($item->quantity === 5);
    }

    public function testConstructorSetsDefaults()
    {
        $itemData = [
            'name' => 'foo',
        ];

        $item = new CartItem($itemData);

        $this->assertTrue($item->price === 0.00);
        $this->assertTrue($item->tax === 0.00);
        $this->assertTrue($item->quantity === 1);
    }

    public function testQuantityMustBeInteger()
    {
        $item = new CartItem();

        $this->setExpectedException('InvalidArgumentException');

        $item->quantity = 'one';
    }

    public function testPriceAndTaxMustBeNumeric()
    {
        $item = new CartItem();

        $this->setExpectedException('InvalidArgumentException');
        $item->price = 'ten';

        $this->setExpectedException('InvalidArgumentException');
        $item->tax = 'ten';
    }

    public function testPriceAndTaxAreCastToFloats()
    {
        $item = new CartItem();

        $item->price = '10.00';
        $item->tax   = '5.00';

        $this->assertTrue(is_float($item->price));
        $this->assertTrue(is_float($item->tax));
    }

    public function testGettingIdPropertyReturnsItemId()
    {
        $item = new CartItem();

        $this->assertSame($item->getId(), $item->id);
    }

    public function testSetReturnsNewId()
    {
        $item = new CartItem();

        $newId  = $item->set('name', 'foo');
        $itemId = $item->id;

        $this->assertSame($newId, $itemId);
    }

    public function testGetSinglePrice()
    {
        $item = new CartItem();

        $item->price = 10.00;
        $item->tax   = 5.00;

        $price = $item->getSinglePrice();

        $this->assertEquals(15.00, $price);
        $this->assertTrue(is_float($price));
    }

    public function testGetSinglePriceExcludingTax()
    {
        $item = new CartItem();

        $item->price = 10.00;
        $item->tax   = 5.00;

        $price = $item->getSinglePriceExcludingTax();

        $this->assertEquals(10.00, $price);
        $this->assertTrue(is_float($price));
    }

    public function testGetTotalPrice()
    {
        $item = new CartItem();

        $item->price    = 10.00;
        $item->tax      = 5.00;
        $item->quantity = 2;

        $price = $item->getTotalPrice();

        $this->assertEquals(30.00, $price);
        $this->assertTrue(is_float($price));
    }

    public function testGetTotalPriceExcludingTax()
    {
        $item = new CartItem();

        $item->price    = 10.00;
        $item->tax      = 5.00;
        $item->quantity = 2;

        $price = $item->getTotalPriceExcludingTax();

        $this->assertEquals(20.00, $price);
        $this->assertTrue(is_float($price));
    }

    public function testGetTotalTax()
    {
        $item = new CartItem();

        $item->quantity = 2;
        $item->tax      = 5.00;

        $tax = $item->getTotalTax();

        $this->assertEquals(10.00, $tax);
        $this->assertTrue(is_float($tax));
    }

    public function testGetSingleTax()
    {
        $item = new CartItem();

        $item->quantity = 2;
        $item->tax      = 5.00;

        $tax = $item->getSingleTax();

        $this->assertEquals(5.00, $tax);
        $this->assertTrue(is_float($tax));
    }

    public function testGetSaveProvider()
    {
        return [
            //$productID, $discount, $price, $save, $savePercent
            [
                22, 4, 8, 6, 60
            ],
            [
                22, 0, 8, 2, 20
            ],
            [
                22, 8, 8, 10, 100
            ],
            [
                25, 0, 40, 10, 20
            ],
            [
                25, 15, 40, 25, 50
            ],
            [
                25, 40, 40, 50, 100
            ],
            [
                26, 0, 5, 5, 50
            ],
            [
                26, 2, 5, 7, 70
            ],
            [
                26, 5, 5, 10, 100
            ],
            [
                27, 0, 37, 13, 26
            ],
            [
                27, 3, 37, 16, 32
            ],
            [
                27, 37, 37, 50, 100
            ],


        ];
    }

    /**
     * @dataProvider testGetSaveProvider
     */
    public function testGetSave($productID, $discount, $price, $save, $savePercent)
    {
        $catalog = $this->getCatalog();
        $product = $catalog->getProduct($productID);
        $item    = $catalog->getCartItem($product);

        $item->setDiscount($discount);

        $this->assertEquals($save, $item->getSave(), 'save is bad');
        $this->assertEquals($price, $item->getPrice());
        $this->assertEquals($item->getPrice() - $item->getDiscount(), $item->getPriceWithDiscount());
        $this->assertEquals($price - $discount, $item->getPriceWithDiscount());
        $this->assertEquals($savePercent, $item->getSavePercent(), 'percent is bad');
    }
}
