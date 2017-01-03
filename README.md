# Cart

[![Packagist](https://img.shields.io/packagist/v/mike182uk/cart.svg?style=flat-square)](https://packagist.org/packages/mike182uk/cart)
[![Build Status](https://img.shields.io/travis/mike182uk/cart.svg?style=flat-square)](http://travis-ci.org/mike182uk/cart)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/mike182uk/cart.svg?style=flat-square)](https://scrutinizer-ci.com/g/mike182uk/cart/)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/1d82048a-1390-42d5-8605-606541e81c98.svg?style=flat-square)](https://insight.sensiolabs.com/projects/1d82048a-1390-42d5-8605-606541e81c98)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/mike182uk/cart.svg?style=flat-square)](https://scrutinizer-ci.com/g/mike182uk/cart/)
[![Total Downloads](https://img.shields.io/packagist/dt/mike182uk/cart.svg?style=flat-square)](https://packagist.org/packages/mike182uk/cart)
[![License](https://img.shields.io/github/license/mike182uk/cart.svg?style=flat-square)](https://packagist.org/packages/mike182uk/cart)

A flexible and modern shopping cart package.

## Prerequisites

- PHP >=5.6.0

## Installation

```bash
composer require mike182uk/cart
```

## Usage

- [Cart](#cart)
- [Cart Item](#cart-item)
- [Cart Storage Implementation](#cart-store)

### <a id="cart"></a>Cart

#### Create a new cart

To create a new cart instance you must pass an id and a storage implementation to the cart constructor:

```php
use Cart\Cart;
use Cart\Storage\SessionStore;

$id = 'cart-01';
$cartSessionStore = new SessionStore();

$cart = new Cart($id, $cartSessionStore);
```

The storage implementation must implement `Cart\Storage\Store`.

The id is used for saving / restoring cart state via the storage implementation.

#### Add an item to the cart

Use the `add` method to add an item to the cart. A valid `Cart\CartItem` must be passed to this method.

```php
use Cart\CartItem;

$item = new CartItem;
$item->name = 'Macbook Pro';
$item->sku = 'MBP8GB';
$item->price = 1200;
$item->tax = 200;

$cart->add($item);
```

If the item already exists in the cart, the quantity of the existing item will be updated to include the quantity of the item being added.

#### Remove an item from the cart

Remove an item from the cart by passing the item id to the `remove` method.

```php
$cart->remove('e4df90d236966195b49b0f01f5ce360a356bc76b');
```

#### Update an item in the cart

To update a property of an item in the cart use the `update` method. You will need to pass the cart item id, the name of the property to update and the new value. This method will return the item id (in case it has changed due to the update).

```php
$newId = $cart->update('e4df90d236966195b49b0f01f5ce360a356bc76b', 'price', 959.99);
```

If you try and update an item that does not exist in the cart a `InvalidArgumentException` will be thrown.

#### Retrieve an item in the cart

Retrieve an item from the cart by its id use the `get` method. If the item does not exist `null` is returned.

```php
$item = $cart->get('e4df90d236966195b49b0f01f5ce360a356bc76b');

if ($item) {
    // ...
}
```

#### Retrieve all items in the cart

Retrieve all items in the cart using the `all` method. This will return an array of all the items in the cart.

```php
$cartItems = $cart->all();

if (count($cartItems) > 0) {
    foreach ($cartItems as $item) {
        // ...
    }
}
```

#### Determine if an item exists in the cart

Determine if an item exists in the cart using the `has` method. Returns `true` or `false`.

```php
if ($cart->has('e4df90d236966195b49b0f01f5ce360a356bc76b')) {
    // ...
}
```

#### Clear The Cart

Clear the cart using the `clear` method.

```php
$cart->clear();
```
This will also clear the saved state for this cart in the store.

#### Save / restore cart state

Save the cart using the `save` method.

```php
$cart->save();
```

This will save the current cart items and cart id to the store.

Restore the cart using the `restore` method.

```php
$cart->restore();
```

This will add any stored cart items back to the cart and set the cart id. If there is a problem restoring the cart a `Cart\CartRestoreException` will be thrown. This will only happen if:

- the saved data is unserializable
- the unserialized data is invalid (not an array)
- the cart id is not present in the unserialized data
- the cart items are not present in the unserialized data
- the cart id is invalid (not a string)
- the cart items are invalid (not an array)

#### Other Cart Methods

##### totalUniqueItems

Get the total number of unique items in the cart.

```php
$cart->totalUniqueItems();
```

##### totalItems

Get the total number of items in the cart.

```php
$cart->totalItems();
```

##### total

Get the total price of all the cart items including tax.

```php
$cart->total();
```

You can get the total excluding tax by using the `totalExcludingTax` method.

```php
$cart->totalExcludingTax();
```

##### tax

Get the total tax of all the cart items.

```php
$cart->tax();
```

##### toArray

Export the cart to an array.

```php
$cartData = $cart->toArray();
```

Array will be structured like:

```php
[
    'id' => 'cart-01', // cart id
    'items' => [
        // cart items as array
    ]
]
```

##### getId

Get the id of the cart.

```php
$cart->getId();
```

##### getStore

Get the cart storage implementation.

```php
$cart->getStore();
```

### <a id="cart-item"></a>Cart Item

#### Create a new Cart Item

```php
use Cart\CartItem;

$item = new CartItem;

$item->name = 'Macbook Pro';
$item->sku = 'MBP8GB';
$item->price = 1200;
$item->tax = 200;
$item->options = [
    'ram' => '8 GB',
    'ssd' => '256 GB'
];
```

`Cart\CartItem` implements `ArrayAccess` so properties can be assigned to the cart item as if accessing an array:

```php
$item = new CartItem;

$item['name'] = 'Macbook Pro';
$item['sku'] = 'MBP8GB';
$item['price'] = 1200;
$item['tax'] = 200;
$item['options'] = [
    'ram' => '8 GB',
    'ssd' => '256 GB'
];
```

An array of data can also be passed to the cart item constructor to set the cart item properties:

```php
$itemData = [
    'name' => 'Macbook Pro';
    'sku' => 'MBP8GB';
    'price' => 1200;
    'tax' => 200;
    'options' => [
        'ram' => '8 GB',
        'ssd' => '256 GB'
    ]
];

$item = new CartItem($itemData);
```

If no quantity is passed to the cart item constructor, the quantity is set to `1` by default.

If no price is passed to the cart item constructor, the price is set to `0.00` by default.

If no tax is passed to the cart item constructor, the tax is set to `0.00` by default.

#### Cart Item ID

Each cart has a unique ID. This ID is generated using the properties set on the cart item. You can get the cart item ID using the method `getId` or by accessing the property `id`.

```php
$id = $item->getId();
```

```php
$id = $item->id;
```

```php
$id = $item['id'];
```

**Changing a property on the cart item will change its ID.**

#### Cart Item Methods

#### get

Get a piece of data set on the cart item.

```php
$name = $item->get('name');
```

This is the same as doing:

```php
$name = $item['name'];
```

```php
$name = $item->name;
```

#### set

Set a piece of data on the cart item.

```php
$item->set('name', 'Macbook Pro');
```

This is the same as doing:

```php
$item['name'] = 'Macbook Pro';
```

```php
$item->name = 'Macbook Pro';
```

If you are setting the item quantity, the value must be an integer otherwise an `InvalidArgumentException` is thrown.

```php
$item->quantity = 1; // ok

$item->quantity = '1' // will throw exception
```

If you are setting the item price or tax, the value must be numeric otherwise an `InvalidArgumentException` is thrown.

```php
$item->price = 10.00; // ok

$item->price = '10' // ok

$item->price = 'ten' // will throw exception
```

##### getTotalPrice

Get the total price of the cart item including tax `((item price + item tax) * quantity)`.

```php
$item->getTotalPrice();
```

You can also get the total price excluding tax `(item price * quantity)` using the `getTotalPriceExcludingTax` method.

```php
$item->getTotalPriceExcludingTax();
```

##### getSinglePrice

Get the single price of the cart item including tax `(item price + item tax)`

```php
$item->getSinglePrice();
```

You can also get the single price excluding tax by using the `getSinglePriceExcludingTax` method.

```php
$item->getSinglePriceExcludingTax();
```

##### getTotalTax

Get the total tax of the cart item `(item tax * quantity)`.

```php
$item->getTotalTax();
```

##### getSingleTax

Get the single tax value of the cart item.

```php
$item->getSingleTax();
```

##### toArray

Export the item to an array.

```php
$itemArr = $item->toArray();
```

Array will be structured like:

```php
[
    'id' => 'e4df90d236966195b49b0f01f5ce360a356bc76b', // cart item unique id
    'data' => [
        'name' => 'Macbook Pro',
        'sku' => 'MBP8GB',
        'price' => 1200,

        // ... other cart item properties
    ]
]
```

### <a id="cart-store"></a>Cart Storage Implementation

A cart storage implementation must implement `Cart\Storage\Store`.

This package provides 2 basic storage implementations: `Cart\Storage\SessionStore` and `Cart\Storage\CookieStore`.

When the `save` method of the cart is called, the cart id and serialized cart data is passed to the `put` method of the storage implementation.

When the `restore` method of the cart is called, the cart id is passed to the `get` method of the storage implementation.

When the `clear` method of the cart is called, the cart id is passed to the `flush` method of the storage implementation.

An example session storage implementation may look like:

```php
use Cart\Store;

class SessionStore implements Store
{
    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        return isset($_SESSION[$cartId]) ? $_SESSION[$cartId] : serialize([]);
    }

    /**
     * {@inheritdoc}
     */
    public function put($cartId, $data)
    {
        $_SESSION[$cartId] = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function flush($cartId)
    {
        unset($_SESSION[$cartId]);
    }
}
```
