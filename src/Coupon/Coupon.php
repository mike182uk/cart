<?php

namespace Cart\Coupon;

use Cart\Arrayable;

class Coupon implements Arrayable
{
    public $code;

    public function getCode()
    {
        return $this->code;
    }

    public function toArray()
    {
        return array(
            'code'  =>  $this->code,
        );
    }
}
