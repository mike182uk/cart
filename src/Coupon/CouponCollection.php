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
            $coupon->setCode($p['code']);
            $coupon->setType($p['type']);

            if (isset($p['products']) && !empty($p['products'])) {
                $coupon->setProducts($p['products']);
            }
            if (isset($p['config']) && !empty($p['config'])) {
                $coupon->setConfig($p['config']);
            }

            if (isset($p['valid_from']) && !empty($p['valid_from'])) {
                $coupon->setValidFrom($p['valid_from']);
            }

            if (isset($p['valid_until']) && !empty($p['valid_until'])) {
                $coupon->setValidUntil($p['valid_until']);
            }

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
            if ($code === $item->getCode()) {
                return $item;
            }
        }

        throw new \InvalidArgumentException('Coupon not found');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->coupons);
    }

    public function jsonSerialize()
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
