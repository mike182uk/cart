<?php

namespace Cart;

use Cart\Coupon\Coupon;

class Checkout
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * @var string
     */
    private $country = 'US';

    /**
     * Checkout constructor.
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @return Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * @param Coupon $coupon
     */
    public function setCoupon(Coupon $coupon)
    {
        $this->coupon = $coupon;
        $coupon->calculateDiscount($this->cart);
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getTotalIcannFees()
    {
        return $this->cart->icann();
    }

    public function getTotalTaxes()
    {
        return $this->cart->tax();
    }

    public function getTotalSavings()
    {
        return $this->cart->totalSavings() + $this->cart->getDiscount();
    }

    public function getTotal()
    {
        return $this->cart->total() + $this->cart->icann() - $this->cart->getDiscount();
    }
}
