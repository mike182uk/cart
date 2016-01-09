<?php

namespace Cart\Coupon;

use Cart\Arrayable;

use Cart\Cart;

class Coupon implements Arrayable
{
    private $code;

    private $products = array();

    private $type = 'PercentDiscount';

    private $config = array();

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Product ids for which this coupon applies
     * @param array $products
     */
    public function setProducts(array $products)
    {
        $this->products = $products;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function calculateDiscount(Cart $cart)
    {
        $types = array(
            'PercentDiscount',
            'AmountDiscount',
            'FreeAddon',
            'BogoHalf',
            'OverAmount',
        );
        if (in_array($this->type, $types)) {
            $type = $this->type;
        } else {
            $type = $types[0];
        }

        $ft = __NAMESPACE__ . '\\Coupon' . $type;
        $calc = new $ft($cart);
        $calc->calculateDiscount($this, $cart);
    }

    public function toArray()
    {
        return array(
            'code' => $this->code,
            'type' => $this->type,
            'products' => $this->products,
            'config' => $this->config,
        );
    }
}