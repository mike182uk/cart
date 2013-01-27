<?php

namespace Cart\Facade;

use Cart\Manager as CartManager;

class Manager
{
    /**
     * Cart manager instance
     *
     * @var \Cart\Manager
     */
    protected static $cartManager;

    /**
     * Initialises the cart manager facade
     *
     * @param  \Cart\Manager $cartManager Cart manager instance
     * @return void
     */
    public static function init(CartManager $cartManager)
    {
        static::$cartManager = $cartManager;
    }

    /**
     * Proxy a static method call to the cart manager instance
     *
     * @param  string                  $method Method name
     * @param  array                   $args   Arguments to pass to method
     * @return mixed
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $args)
    {
        if (method_exists(static::$cartManager, $method)) {
            return call_user_func_array(array(static::$cartManager, $method), $args);
        } else {
            throw new \BadMethodCallException(sprintf('Method: %s::%s does not exist', get_called_class(), $method));
        }
    }
}
