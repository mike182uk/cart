<?php

namespace Cart\Storage;

interface Store
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
     */
    public function put($cartId, $data);

    /**
     * Flush the saved state for a cart instance.
     *
     * @param string $cartId
     */
    public function flush($cartId);
}
