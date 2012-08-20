#Cart

A composer compatible, modern shopping cart for PHP >=5.3.0

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

The cart can be used with or without the manager component. If you choose not to use the manager component you will have to manage your storage implementation manually and you will not beable to use the proxy (unless you extend and modify yourself).

The storage component is swapable. If you have a certain way you need to implement state persistence you can do this by implementing the storage interface: ``\Cart\Storage\StorageInterface``.

By default 1 storage component is provided:

1. ``\Cart\Storage\Session`` - persists state using the session