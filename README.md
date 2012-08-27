#Cart

A modern, composer compatible, PHP >=5.3.0 shopping cart

###Features

- Flexible, extensible, component based architecture
- Handles one or several cart instances (via cart manager)
- Configurable cart and cart items
- Cart and cart items support meta data
- Flexible state persistence
- Namespaced, composer ready, framework independent, PSR-0

###Prerequisites

1. PHP >=5.3.0
3. This package can be installed using composer or can be integrated manually. If you are not using an autoloader make sure you include all of the php files in the ``src`` directory.

```
require '<path-to-src>/Cart/Storage/Interface.php';
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

The storage component is swapable. If you have a certain way you need to implement state persistence you can do this by implementing the storage interface: ``\Cart\Storage\StorageInterface``.

By default 1 storage component is provided:

1. ``\Cart\Storage\Session`` - persists state using the session

##Using The Cart Manager

This section will guide you through using the cart component **with** the cart manager component. 9 Times out of the 10 this will be the setup you want to use.

### Aliases

I recommend aliasing the manager and proxy components to something easier to write and reference:

```
use \Cart\Manager as CartManager;
use \Cart\Proxy as Cart;

// this will enable you make calls like:

$numOfItmes = Cart::itemCount():

CartManager::destroyInstance();

```
***Note:*** *The rest of the code examples in this section will assume the above has been done.* 

### Configuration

You will need to pass an array of configuration options to the cart managers init method. This kick starts the manager. This should be the first thing you do before you try and use the cart manager. The configuration options would be best saved in their own file and included into the script when needed:

```
<?php

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
        //'cart3' => array()
    )

);
```

```
$config = include '<path-to-config>/config.php';

CartManager::init($config);
```

From the configuration file you can define multiple cart instances. Each instance can have its own unique set of properties, otherwise it will just inherit from the default options.

### Context

The cart manager can only manage 1 cart instance at a time. This cart will be the cart that is in the current context. If you have multiple carts you can switch between them. This is known as switching context. You can control the context using the ``context`` method of the cart manager component:

```
CartManager::context('Cart-02'); //switches the context to cart 2. Cart-02 is the ID of the cart as specified in the configuration file.
```

The cart proxy component makes use of the cart manager to retrieve the current cart in context. This is what allows you to directly make calls on a cart instance:

```
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
- **storage_key_prefix**, **storage_key_suffix** - These are strings that will be added to the cart ID, which will be used as the identifier in your storage implementation. *i.e If you are using the session to save the cart state, this would look something like ```$_SESSION['<cart_storage_prefix><cart_id><cart_storage_suffix>']``` or in our example above ```$_SESSION['cart_Cart-01_instance']```*.

If you chose to autosave (recommended), internally this is registered as a shutdown function:

```
register_shutdown_function(array('\Cart\Manager', 'saveState'), $cartID);
```

The state is restored in the when ``CartManager::init()`` is called.

##Not Using The Cart Manager

This section will guide you through using the cart **without** the cart manager.
