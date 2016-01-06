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

    public function hasTrial()
    {
        return $this->trial >=0 && $this->trial != $this->price;
    }

    public function getTotalPrice()
    {
        if ($this->hasTrial()) {
            return ($this->period - 1) * $this->price + $this->trial;
        }
        return $this->price * $this->period;
    }

    public function getSave()
    {
        $price = $this->price;
        $old = $this->old;
        if ($old > $price) {
            return ($old - $price) * $this->period;
        }
        return 0;
    }

    public function getSavePercent()
    {
        if ($this->getSave() != 0) {
            $price = $this->price;
            $old = $this->old;
            return ($old - $price) / $price * 100;
        }
        return 0;
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
