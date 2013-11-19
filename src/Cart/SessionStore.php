<?php

namespace Cart;

use Cart\StoreInterface;

class SessionStore implements StoreInterface
{
    /**
     * Retrieve the saved state for a cart instance.
     *
     * @param  string $cartId
     * @return string
     */
    public function get($cartId)
    {
        return isset($_SESSION[$cartId]) ? $_SESSION[$cartId] : array();
    }

    /**
     * Save the state for a cart instance.
     *
     * @param  string $cartId
     * @param  string $data
     * @return void
     */
    public function put($cartId, $data)
    {
        $_SESSION[$cartId] = $data;
    }

    /**
     * Flush the saved state for a cart instance.
     *
     * @param  string $cartId
     * @return void
     */
    public function flush($cartId)
    {
        unset($_SESSION[$cartId]);
    }
}
