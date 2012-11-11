#Cart

A modern, composer compatible, PHP >=5.3.0 shopping cart

###Features

- Flexible, extensible, component based architecture
- Handles one or several cart instances (via cart manager)
- Configurable cart and cart items
- Cart and cart items support meta data
- Importable / Exportable carts
- Flexible state persistence
- Namespaced, composer ready, framework independent, PSR-2

###Prerequisites

1. PHP >=5.3.0
3. This package can be installed using composer or can be integrated manually. If you are not using an autoloader make sure you include all of the php files in the ``src`` directory.

```php
require '<path-to-src>/Cart/Storage/StorageInterface.php';
require '<path-to-src>/Cart/Storage/Session.php';
require '<path-to-src>/Cart/Cart.php';
require '<path-to-src>/Cart/Item.php';
require '<path-to-src>/Cart/Manager.php';
require '<path-to-src>/Cart/Proxy.php';
```
***Note:*** *All of the code examples in the rest of this document assume you have the above files above included manually or autoloaded. If you are using your own storage component you do not have to include ``<path-to-src>/Cart/Storage/Session.php``*

###Architecture

This package is built out of a few components that work together:

1. Cart Item - Item that will be stored in the cart
2. Cart - Stores cart items
3. Manager - Manages multiple cart instances + state persistence
4. Proxy - Proxies static method calls to a cart instance (the one in the current context)
5. Storage - Manages persistence of a carts state

The cart component can be used with or without the manager component. If you choose **not** to use the manager component you will have to manage your storage implementation manually and you will not beable to use the proxy component (unless you extend and modify yourself).

The storage component is swapable. If you have a certain way you need to implement state persistence you can do this by implementing the storage interface: ``\Cart\Storage\StorageInterface`` (see [custom state persistence](#custom-state-persistence)).

By default 1 storage component is provided:

1. ``\Cart\Storage\Session`` - persists state using the session

## Setup: Using The Cart Manager

This section will guide you through setting up the cart component **with** the manager component. 9 Times out of the 10 this will be the setup you want to use.

### Aliases

I recommend aliasing the manager and proxy components to something easier to write and reference:

```php
use \Cart\Manager as CartManager;
use \Cart\Proxy as Cart;

// this will enable you make calls like:

$numOfItmes = Cart::itemCount():

CartManager::destroyCart();
```
***Note:*** *The rest of the code examples in this section will assume the above has been done.* 

### <a id="configuration"></a> Configuration

You will need to pass an array of configuration options to the cart managers ``init`` method. This kick starts the manager. This should be the first thing you do before you try and use the cart manager. The configuration options would be best saved in their own file and included into the script when needed:

```php
//config.php

return array(

    'defaults' => array(
        /**
         * The decimal character to be used when formatting numbers
         */
        'decimal_point' => '.',

        /**
         * The number of decimal places after a number
         */
        'decimal_places' => 2,

        /**
         * The thousands separator character to be used when formatting numbers
         */
        'thousands_separator' => ',',

        /**
         * Storage configuration options
         */
        'storage' => array(
            /**
             * Should the cart autosave at the end of script execution. If true, need
             * to define storage driver
             */
            'autosave' => true,
            /**
             * The name of the driver to use. This should be the name of the file handling the
             * storage driver in the storage/driver directory
             *
             * i.e session, cookie etc.
             */
            'driver'=> 'session', //session, cookie
            /**
             * This is prepended to the storage identifier for the cart instance
             */
            'storage_key_prefix' => 'cart_',
            /**
             * This is appended to the storage identifier for the cart instance
             */
            'storage_key_suffix' => '_instance'
        )
    ),

    /**
     * Carts to be instantiated when the cart manager is initialized. Each instance
     * will inherit the default values above if not specified
     */
    'carts' => array(
        'Cart-01' => array(), //will inherit all of the above options
        'Cart-02' => array(),
    )

);
```

```php
$config = include '<path-to-config>/config.php';

CartManager::init($config);
```

From the configuration file you can define multiple cart instances. Each instance can have its own unique set of properties, otherwise it will just inherit from the default options.

Internally the cart and cart item components use ``number_format()`` to format currency values. The configuration options ``decimal_point``, ``decimal_places`` and ``thousands_separator`` all relate to this.

### <a id="context"></a>Context

The cart manager can only manage 1 cart instance at a time. This cart will be the cart that is in the current **context**. If you have multiple carts you can switch between them. This is known as switching context. You can control the context using the ``context`` method of the manager component:

```php
CartManager::context('Cart-02'); //switches the context to cart 2. Cart-02 is the ID of the cart as specified in the configuration file.
```

The cart proxy component makes use of the cart manager to retrieve the current cart in context. This is what allows you to directly make calls on a cart instance:

```php
CartManager::context('Cart-02'); //switches the context to cart 2

$numOfItemsCart1 = Cart::itemsCount(); //get the number of items in cart 2

CartManager::context('Cart-01');

$numOfItemsCart2 = Cart::itemsCount(); //get the number of items in cart 1
```

By default ``CartManager::init()`` will set the first cart in the ``carts`` array, in the configuration file, to be the cart in context (in our example above this will be ``Cart-01``);

### State Persistance (Storage)

You can define the storage options in the configuration file. 

- **autosave** - If set to true, the cart state will be saved automatically with out you having to do anything. If set to false, the state can be saved manually by calling ``CartManager::saveState()``.
- **driver** - This is the name of the class that contains your storage implementation. This class should implement ``\Cart\Storage\StorageInterface``. If this option is set to a blank string, the cart manager will not attempt to preserve or restore state.
- **storage_key_prefix**, **storage_key_suffix** - These are strings that will be added to the cart ID, which will be used as the identifier in your storage implementation. *i.e If you are using the session to save the cart state, this would look something like ```php$_SESSION['<cart_storage_prefix><cart_id><cart_storage_suffix>']```php or in our example above ```php$_SESSION['cart_Cart-01_instance']```php*.

If you chose to autosave (recommended), internally this is registered as a shutdown function:

```php
register_shutdown_function(array('\Cart\Manager', 'saveState'), $cartID);
```

The state is restored when ``CartManager::init()`` is called.

##Setup: Not Using The Cart Manager

This section will guide you through setting up the cart **without** the cart manager.

You can use the cart component without having to use the cart manager. You may want to do this for simpler setups where you do not need all the bells and whistles of the cart manager. The drawbacks to not using the cart manager are:

1. You will have manage state persistance manually
2. You will not have access to the proxy
3. You cannot manage multiple cart instances from one central place

### Configuration

You will need to pass an array of configuration options to the carts ``constructor``. This kick starts the cart. This should be the first thing you do before you try and use the cart. The configuration options would be best saved in their own file and included into the script when needed:

```php
//config.php

return array(

	/**
	 * The decimal character to be used when formatting numbers
	 */
	'decimal_point' => '.',
	
	/**
     * The number of decimal places after a number
     */
     'decimal_places' => 2,

    /**
     * The thousands separator character to be used when formatting numbers
     */
     'thousands_separator' => ','
);
```

```php
$config = require '<path-to-config>/config.php';

// the carts constructor takes the cart id and the conifg options
// as arguments. if the cart id is set to false then the cart will 
// automatically generate an id for this cart instance
$cart = new \Cart\Cart('cart01', $config);
```

### Manual State Persistance (Storage)

The cart component has ``import`` and ``export`` methods that you can use to manually control the state of the cart:

```php
// saving cart state using sessions
$cartState = $cart->export();
$_SESSION['cart'] = $cartState;

…

// importing cart state using sessions
$cart->import($_SESSION['cart']);
```

The ``export`` method returns an array of all the of carts items and any meta data stored on the cart.

The ``import`` method expects an array formatted the same as what ``export`` produces.

You are free to use whatever storage implementation you want as long as the data that is being imported is compatible.

## Useage

### Adding an item to the cart

You define an item with a simple array:

```php
$item = array(
	'name' => 'Apple Macbook Pro 13 inch Laptop',
	'sku' => 'B004P8JCY8',
	'id' => '2',
	'price' => '824.17',
	'tax' => '164.83',
	'weight' => '3900',
	'quantity' => 1
);
```

You can have as many or as little properties as required (it is always a good idea to have price, tax and name though). If a quantity is not supplied, the assumed quantity is 1.

The item is then passed to the carts ``add``:

```php
// using the cart manager
Cart::add($item);

// not using the cart manager
$cart->add($item);
```

If an item is added to the cart that has already been added before, the quantity for that item is automatically updated. The same item is not added again, unless its properties are different (i.e has a different price or sku).

Internally each item is assigned a UID (unique identifier) and you will use this to interact with items already in the cart. The carts `add` method returns the UID of the new item that is added:

```php
$itemUID = Cart::add($item);

// $itemUID = 1b5792a046e0597daa4186169adb8d66
```

### Removing an item from the cart

To remove an item you need to pass the items UID to the carts ``remove`` method:

```php
$uid = '1b5792a046e0597daa4186169adb8d66';

// using the cart manager
Cart::remove($uid);

// not using the cart manager
$cart->remove($uid);
```

### Updating an item in the cart

To update an item you need to pass the items UID, the property to update and the new value of the property to the carts ``update`` method:

```php
$uid = '1b5792a046e0597daa4186169adb8d66';

try {
	// using the cart manager
	Cart::update($uid, 'quantity', 10);

	// not using the cart manager
	$cart->update($uid, 'quantity', 10);
}
```

If any property other than the qauntity is changed then the UID will be recalculated:

```php
$uid = '1b5792a046e0597daa4186169adb8d66';

try {
	$uid = Cart::update($uid, 'price', '900.00');
}

// $uid = 35a1699a3471501c6927cb93aa181c88

```

A ``\Cart\Exception\InvalidCartItemException`` exception is thrown if the item you are trying to update does not exist (see below for how to check if an item exists).

**Tip:** You can also remove an item from the cart by setting its quantity to zero using the ``update`` method.

### Checking if an item exists in the cart

You can check if an item exists in cart using the carts ``exists`` method:

```php
$uid = '1b5792a046e0597daa4186169adb8d66';

// using the cart manager
$itemExists = Cart::exists($uid);

// not using the cart manager
$itemExists = $cart->exists($uid);
```

You can also pass an item array to the ``exists`` method:

```php
$item = array(
	'name' => 'Apple Macbook Pro 13 inch Laptop',
	'sku' => 'B004P8JCY8',
	'id' => '2',
	'price' => '824.17',
	'tax' => '164.83',
	'weight' => '3900',
	'quantity' => 1
);

if (Cart::exists($item))
{
	//...
}
```

You may want to use this to check if an item has already been added to the cart before trying to add it.

### Getting the total number of items in the cart

```php
// using the cart manager
Cart::itemCount();

// not using the cart manager
$cart->itemCount();
```

You can also get the total **unique** number of items in the cart (quantity is ignored) by passing ``true`` to the carts ``itemCount`` method:

```php
Cart::itemCount(true);
```

### Getting items in the cart

The carts ``items`` method returns an array of ``\Cart\Item`` objects:

```php
// using the cart manager
$items = Cart::items();

// not using the cart manager
$items = $cart->items();
```

If you just want 1 item you can use the carts ``item`` method:

```php
$uid = '1b5792a046e0597daa4186169adb8d66';

try {
	// using the cart manager
	$item = Cart::item($uid);

	// not using the cart manager
	$item = $cart->item($uid);
}
```

The carts ``item`` method throws a ``\Exception\InvalidCartItemException`` exception if the item does not exist in the cart.

### Getting the total cart value

The carts ``total`` method returns the total value of the cart. This returns the total value **including** tax. You can return the total without tax by passing ``true`` as a parameter:

```php
// using the cart manager
$total = Cart::total();
$totalExcludingTax = Cart::total(true);

// not using the cart manager
$total = $cart->total();
$totalExcludingTax = $cart->total(true);
```

To just get the total tax you can use the ``tax`` method:

```php
// using the cart manager
$tax = Cart::tax();

// not using the cart manager
$tax = $cart->tax();
```

### Getting cart totals

If the items in the cart have countable properties (weight etc.) you can use the carts ``getTotal`` method to get the carts total value for this property:

```php
// using the cart manager
$totalWeight = Cart::getTotal('weight');

// not using the cart manager
$totalWeight = $cart->getTotal('weight');
```

Internally the carts ``itemCount``, ``total`` and ``tax`` methods use this method.

The carts ``getTotal`` method can be used in other ways - i.e to check if the cart has any discounted items:

```php
$item1 = array(
	'name' => 'Apple Macbook Pro 13 inch Laptop',
	'sku' => 'B004P8JCY8',
	'id' => '2',
	'price' => '824.17',
	'tax' => '164.83',
	'weight' => '3900',
	'quantity' => 1
);

$item2 = array(
	'name' => 'Apple Macbook Pro 13 inch Laptop - Discounted',
	'sku' => 'B004P8JCY8',
	'id' => '2',
	'price' => '724.17',
	'tax' => '164.83',
	'weight' => '3900',
	'quantity' => 1,
	'discounted' => 1
);

$item2 = array(
	'name' => 'Apple Macbook Pro 13 inch Laptop - Heavily Discounted',
	'sku' => 'B004P8JCY8',
	'id' => '2',
	'price' => '624.17',
	'tax' => '164.83',
	'weight' => '3900',
	'quantity' => 1,
	'discounted' => 1
);

Cart::add($item1);
Cart::add($item2);

$hasDiscounts = Cart::getTotal('discounted') > 0; // will be true as 2 items have the discounted property

if ($hasDiscounts)
{
    //...
}
```

### Clearing the cart

```php
// using the cart manager
Cart::destroyCart();

// not using the cart manager
$cart->clear();
```

### <a id="cart-meta-data"></a>Cart Meta Data

You store meta data on the cart. This can be useful for things like recording a customers checkout message, or keeping track of applied discounts etc.

##### Setting meta data

```php
// using the cart manager
Cart::setMeta('checkout_message', 'This is a checkout message');

// not using the cart manager
$cart->setMeta('checkout_message', 'This is a checkout message');
```

##### Retreiving meta data

```php
// using the cart manager
$checkoutMessage = Cart::getMeta('checkout_message');

// not using the cart manager
$cart->getMeta('checkout_message');
```

##### Removing meta data

```php
// using the cart manager
Cart::removeMeta('checkout_message');

// not using the cart manager
$cart->removeMeta('checkout_message');
```

##### Has meta data

You can also check if the cart has meta data set against it using the ``hasMeta`` method. You can pass a key to this method to check if a specific piece of meta data exists:

```php
// using the cart manager
$hasMeta = Cart::hasMeta();
$hasCheckoutMessage = Cart::hasMeta('checkout_message');

// not using the cart manager
$hasMeta = $cart->hasMeta();
$hasCheckoutMessage = $cart->hasMeta('checkout_message');
```

### Importing / Exporting Carts

The cart has ``import`` and ``export`` methods that you can use:

```php
// using the cart manager

$cartState = Cart::export();
$_SESSION['cart'] = $cartState;

// importing cart state using sessions
Cart::import($_SESSION['cart']);

// not using the cart manager

// saving cart state using sessions
$cartState = $cart->export();
$_SESSION['cart'] = $cartState;

// importing cart state using sessions
$cart->import($_SESSION['cart']);
```

The ``export`` method returns an array of all the of carts items and any meta data stored on the cart.

The ``import`` method expects an array formatted the same as what ``export`` produces.

### Cart Items

The cart items are instances of ``\Cart\Item`` and expose methods for interacting with them:

##### Getting a property

```php
try {

	$item = Cart::item($uid);
	
	$itemName = $item->get('name');
	$itemSKU = $item->get('sku');
}
```

##### Getting the UID

```php
$cartItems = Cart::items();

foreach ($cartItems as $item)
{
	$uid = $item->uid();
}
```

##### Getting the quantity

```php
try {

	$item = Cart::item($uid);
	
	$itemQauntity = $item->quantity();
	// or
	$itemQauntity = $item->get('quantity');
}
```

##### Updating the quantity

You can update an items quantity via the item itself using the items ``setQuantity`` method:

```php
try {
	Cart::item($uid)->setQuantity(10);
}
```

This is the same as doing:

```php
try {
	Cart::update($uid, 'quantity', 10);
}
```

##### Getting the price

The items ``totalPrice`` method returns the total value of the item (taking into account quantity). This returns the total value **including** tax. You can return the total value without tax by passing ``true`` as a parameter:

```php
try {
	$item = Cart::item($uid);
	$itemPrice = $item->totalPrice();
	$itemPriceExcludingTax = $item->totalPrice(true);
}
```

To just get the items total tax you can use the ``totalTax`` method:

```php
try {
	$item = Cart::item($uid);
	$itemTax = $item->totalTax();
}
```

You may want to get just the single price or single tax value for an item, this can be done with the items ``singlePrice`` and ``singleTax`` methods:

```php
try {
	$item = Cart::item($uid);
	$itemSinglePrice = $item->singlePrice();
	$itemSinglePriceExcludingTax = $item->singlePrice(true);
	$itemSingleTax = $item->singleTax();
}
```

##### Meta data

Cart items also support meta data in the same way the cart does (see [above](#cart-meta-data)):

```php
try {
	$item = Cart::item($uid);
	
	// set meta
	$item->setMeta('engraving_text', 'Some engraving text here!');
	
	// get meta
	$engravingText = $item->getMeta('engraving_text');
	
	// remove meta
	$item->removeMeta('engraving_text');
	
	// has meta
	if ($item->hasMeta()) 
	{
		//...
	}
}
```

##### Export

You can export an item using the items ``export`` method. This method returns an array of the items properties and meta data. You can also pass true to the ``export`` method to have the items UID includeded in the export:

```php
try {
	$item = Cart::item($uid);
	
	$data = $item->export();
	$dataWithUID = $item->export(true);
}
```

### Get a cart instance (Cart Manager Only)

You can retrieve a cart instance from the cart manager using the ``getCart`` method. If you do not supply a cart id the cart in the current context (see [context section above](#context)) will be returned. If the cart does not exist a ``\Cart\Exception\InvalidCartInstanceException`` exception is thrown:

```php
try {
	$cartId = 'cart01';

	// get cart with the ID 'cart01'
	$cart01 = CartManager::getCart($cartId);

	// gets the cart in the current context
	$currentCart = CartManager::getCart();
}
```

### Checking a cart exists (Cart Manager Only)

You can check if a cart exists using the cart managers ``exists`` method:

```php
$cartId = 'cart01';

if (CartManager::exists($cartId))
{
	//...
}
```

### Destroy a cart instance (Cart Manager Only)

You can destroy a cart instance using the cart managers ``destroyCart`` method:

```php
$cartId = 'cart01';

CartManager::destroyCart($cartId);
```

If a cart id is not passed then the cart in the current context will be destroyed.

When a cart is destroyed it is removed from the cart manager and its saved state is cleared (via the state persitence handler). If you do not want to clear the carts state you can pass a second parameter of ``true`` to the ``destroyCart`` method:

```php
$cartId = 'cart01';

// cart will be destroyed in the cart manager but its state will be saved
CartManager::destroyCart($cartId, true);
```

You can destroy all carts managed by the cart manager using the ``destroyAllCarts`` method. You can preserve the carts saved state by passing a second parameter of ``true``:

```php
CartManager::destroyAllCarts();

// all carts will be destroyed in the cart manager but there state will be saved 
CartManager::destroyAllCarts();
```

### Create a new cart instance (Cart Manager Only)

You can create a new cart instance using the cart managers ``newCart`` method. This method takes 4 parameters:

1. ``$cartId`` - The ID for this cart
2. ``$config`` - The configuration options for this cart. If false is passed for this parameter the default configuration options will be used (see [configuration section above](#configuration))
3. ``$overwrite`` - Boolean to indicate if a cart with same ID exists, whether it should be overwritten or not
4. ``$switchContext`` - Once the new cart has been created should the current cart context be switched to this cart

##### Simple create a new cart
```php
CartManager::newCart('cart02');
```
##### Create a new cart with custom config

```php
$config = array(
        'decimal_point' => '.',
        'decimal_places' => 2,
        'thousands_separator' => ',',
        'storage' => array(
            'autosave' => true,
            'driver'=> 'cookie',
            'storage_key_prefix' => 'cart_',
            'storage_key_suffix' => '_instance'
        )
    ),

);

CartManager::newCart('cart02', $config);
```
##### Create a new cart but dont overwrite exisiting cart

``newCart`` throws an ``\Cart\Exception\DuplicateCartInstanceException`` exception if ``$overwrite`` is set to false and the cart exists:

```php
try {
	CartManager::newCart('cart02', false, false);
}
```

### Cart state persistance / restoration

You can manually for a cart to save its state using the ``saveCartState`` method:

```php
$cartId = 'cart01';

CartManager::saveCartState($cartId);
```

You can also manually restore state for a cart using the ``restoreCartState`` method:

```php
$cartId = 'cart01';

CartManager::restoreCartState($cartId);
```

## <a id="custom-state-persistence"></a>Custom State Persistence

You can create your own custom state persistence component by extending ``\Cart\Storage\StorageInterface``:

```php
namespace Cart\Storage;

class CustomStorage extends StorageInterface
{
	//...
}
```

Your custom component must be stored: ``<path-to-src>/Cart/Storage/``.

The file must be named after the name of the class: ``<path-to-src>/Cart/Storage/CustomStorage.php``;

In the config you just need to set the storage driver to be your custom component:

```php
$config = array(
        'decimal_point' => '.',
        'decimal_places' => 2,
        'thousands_separator' => ',',
        'storage' => array(
            'autosave' => true,
            'driver'=> 'CustomStorage',
            'storage_key_prefix' => 'cart_',
            'storage_key_suffix' => '_instance'
        )
    ),
);
```

## Notes

For more information on the inner workings i highly encourage you to take a deeper look at the source code.

There is also an demo provided with this repo. I highly recommend you check this out as well for a real world useage demo.