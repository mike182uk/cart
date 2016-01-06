<?php

namespace Cart\Catalog;
use Cart\Arrayable;

class Term implements Arrayable
{
    public $period = 1;

    public $price = 0.00;

    public $trial = 0.00;

    public $renewal = 0.00;

    public $old = 0.00; // fake / original / display

    /**
     * Term constructor.
     * @param int $period
     */
    public function __construct($period = 1)
    {
        $this->period = (int)$period;
    }

    public function getTotalPrice()
    {
        return $this->price * $this->period;
    }

    public function toArray()
    {
        return array(
            'period' => $this->period,
            'old' => $this->old,
            'price' => $this->price,
            'trial' => $this->trial,
            'renewal' => $this->renewal,
        );
    }
}
