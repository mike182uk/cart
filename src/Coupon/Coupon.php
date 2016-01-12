<?php

namespace Cart\Coupon;

use Cart\Arrayable;
use Cart\Cart;

class Coupon implements Arrayable
{
    private $code = "";

    private $products = array();

    private $type = 'PercentDiscount';

    private $config = array();

    private $validFrom = null;

    private $validUntil = null;

    /**
     * Coupon constructor.
     * @param string $code
     */
    public function __construct($code = "")
    {
        $this->code = $code;
    }

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

    /**
     * @param string $validFrom
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;
    }

    /**
     * @return string
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * @param string $validUntil
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;
    }

    /**
     * @return string
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    public function calculateDiscount(Cart $cart)
    {
        $types = [
            'PercentDiscount',
            'AmountDiscount',
            'FreeAddon',
            'BogoHalf',
            'OverAmount',
            'SecondFree',
        ];
        if (in_array($this->type, $types)) {
            $type = $this->type;
        } else {
            $type = $types[0];
        }

        if ($this->isActive()) {
            $ft   = __NAMESPACE__ . '\\Coupon' . $type;
            $calc = new $ft($cart);
            $calc->calculateDiscount($this, $cart);
        }
    }

    public function toArray()
    {
        return [
            'code'        => $this->code,
            'type'        => $this->type,
            'products'    => $this->products,
            'config'      => $this->config,
            'valid_from'  => $this->validFrom,
            'valid_until' => $this->validUntil,
        ];
    }

    public function isActive()
    {
        $time = time();

        if (is_null($this->validFrom) && is_null($this->validUntil)) {
            return true;
        }

        if (is_null($this->validFrom)) {
            return (bool)($time < strtotime($this->validUntil));
        }

        if (is_null($this->validUntil)) {
            return (bool)($time > strtotime($this->validFrom));
        }

        return (bool)(strtotime($this->validFrom) < $time && $time < strtotime($this->validUntil));
    }

    public function __toString()
    {
        return $this->code;
    }
}
