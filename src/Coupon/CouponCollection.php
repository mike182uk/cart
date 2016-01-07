<?php

namespace Cart\Coupon;

use Cart\Arrayable;
use Cart\Coupon\Coupon;

class CouponCollection implements Arrayable, \IteratorAggregate, \JsonSerializable
{
    public $coupons = array();

    public function import(array $array)
    {
        foreach ($array as $p) {
            $coupon = new Coupon();
            $coupon->code = $p['code'];
            $this->addCoupon($coupon);
        }
    }

    public function addCoupon(Coupon $coupon)
    {
        $this->coupons[] = $coupon;
    }

    public function getCoupon($code)
    {
        foreach ($this->coupons as $item) {
            if ($code === $item->code) {
                return $item;
            }
        }

        throw new \InvalidArgumentException('Coupon not found');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->coupons);
    }

    function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return array_map(function (Coupon $coupon) {
            return $coupon->toArray();
        }, $this->coupons);
    }
}
