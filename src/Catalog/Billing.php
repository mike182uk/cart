<?php

namespace Cart\Catalog;

use Cart\Arrayable;

class Billing implements Arrayable
{
    public $terms = array();

    /**
     * @param $period
     * @return Term
     */
    public function getTerm($period)
    {
        return $this->terms[$period];
    }

    public function addTerm(Term $term)
    {
        $this->terms[$term->period] = $term;
    }

    public function getRandomTerm()
    {
        if (empty($this->terms)) {
            throw new \Exception('Billing terms are note defined');
        }
        return $this->terms[array_rand($this->terms)];
    }

    public function getPriceForTerm(Term $term)
    {
        return $this->getTerm($term->period)->getTotalPrice();
    }

    public function getSaveForTerm(Term $term)
    {
        $term = $this->getTerm($term->period);
        $price = $term->price;
        $old = $term->old;
        if ($old > $price) {
            return $old - $price;
        }
        return 0;
    }

    public function getSavePercentForTerm(Term $term)
    {
        if ($this->getSaveForTerm($term) != 0) {
            $term = $this->getTerm($term->period);
            $price = $term->price;
            $old = $term->old;
            return ($old - $price) / $price * 100;
        }
        return 0;
    }

    public function toArray()
    {
        return array_map(function (Term $term) {
            return $term->toArray();
        }, $this->terms);
    }
}
