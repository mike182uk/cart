<?php

namespace Cart\Catalog;

use Cart\Arrayable;

class Term implements Arrayable
{
    public $period = 1;

    public $price = 0.00;

    public $trial = -1;

    public $old = -1;

    /**
     * Term constructor.
     * @param int $period
     */
    public function __construct($period = 1)
    {
        $this->period = $period;
    }

    public function hasTrial()
    {
        return $this->trial >= 0 && $this->trial != $this->price;
    }

    public function getTotalPrice()
    {
        if ($this->hasTrial()) {
            return ($this->period - 1) * $this->price + $this->trial;
        }
        return $this->price * $this->period;
    }

    public function getPeriod()
    {
        return $this->period;
    }

    public function getOld()
    {
        return $this->old;
    }

    public function getTrial()
    {
        return $this->trial;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function toArray()
    {
        return array(
            'period' => $this->period,
            'old' => $this->old,
            'trial' => $this->trial,
            'price' => $this->price,
        );
    }
}
