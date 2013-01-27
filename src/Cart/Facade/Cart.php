<?php

namespace Cart\Facade;

use Cart\Facade\Manager as CartManager;

class Cart
{
    /**
     * Proxy a static method call to the cart instance that is in the current context in the cart manager
     *
     * @param  string                  $method Method name
     * @param  array                   $args   Arguments to pass to method
     * @return mixed
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $args)
    {
        $cart = CartManager::getCart();

        if (method_exists($cart, $method)) {
            return call_user_func_array(array($cart, $method), $args);
        } else {
            throw new \BadMethodCallException(sprintf('Method: %s::%s does not exist', get_called_class(), $method));
        }
    }

    /**
     * Return the instance of the cart requested
     *
     * @param  string     $cartID The id of the cart
     * @return \Cart\Cart Cart instance
     */
    public static function context($cartID)
    {
        return CartManager::getCart($cartID);
    }
}
