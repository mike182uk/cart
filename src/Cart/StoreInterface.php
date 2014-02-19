<?php

namespace Cart;

interface StoreInterface
{
    /**
     * Retrieve the saved state for a cart instance.
     *
     * @param string $cartId
     *
     * @return string
     */
    public function get($cartId);

    /**
     * Save the state for a cart instance.
     *
     * @param string $cartId
     * @param string $data
     * @return void
     */
    public function put($cartId, $data);

    /**
     * Flush the saved state for a cart instance.
     *
     * @param string $cartId
     * @return void
     */
    public function flush($cartId);
}
