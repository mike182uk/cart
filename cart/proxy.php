<?php

namespace Cart;

class Cart_Proxy
{
	/**
	 * Proxies the method call to the cart instance that is in the current context in the cart manager
	 *
	 * @static
	 * @param string $method The name of the method being called
	 * @param array $args The arguments passed to the method
	 * @return mixed The response of the proxied method call
	 */
	public static function __callStatic($method, $args)
	{
		$cart = Cart_Manager::get_cart_instance();
		if (method_exists($cart, $method) || substr($method, 0, 11) == 'cumulative_') {
			return call_user_func_array(array($cart, $method), $args);
		}
		else {
			throw new \BadMethodCallException('Invalid method: ' . get_called_class() . '::' . $method);
		}
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Return the instance of the cart requested
	 *
	 * @static
	 * @param string $cart_id The id of the cart
	 * @return object Cart instance
	 */
	public static function context($cart_id)
	{
		return Cart_Manager::get_cart_instance($cart_id);
	}
}