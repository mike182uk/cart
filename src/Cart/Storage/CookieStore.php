<?php

namespace Cart\Storage;

class CookieStore implements Store
{
    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        return isset($_COOKIE[$cartId]) ? $this->decode($_COOKIE[$cartId]) : array();
    }

    /**
     * {@inheritdoc}
     */
    public function put($cartId, $data)
    {
        $this->setCookie($cartId, $this->encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function flush($cartId)
    {
        $this->unsetCookie($cartId);
    }

    /**
     * Encode data to be saved in cookie.
     *
     * @param array $data
     *
     * @return string
     */
    public function encode($data)
    {
        return base64_encode(serialize($data));
    }

    /**
     * Decode data that has been saved in a cookie.
     *
     * @param string $data
     *
     * @return array
     */
    public function decode($data)
    {
        return unserialize(base64_decode($data));
    }

    /**
     * Set cookie.
     *
     * @param string $name
     * @param array  $data
     */
    private function setCookie($name, $data)
    {
        setcookie($name, $data);
    }

    /**
     * Unset cookie.
     *
     * @param string $name
     */
    private function unsetCookie($name)
    {
        unset($_COOKIE[$name]);
    }
}
