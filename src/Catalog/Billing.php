<?php

namespace Cart\Catalog;

use Cart\Arrayable;

class Billing implements Arrayable, \IteratorAggregate
{
    public $terms = array();

    public function getIterator()
    {
        return new \ArrayIterator($this->terms);
    }

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
            throw new \Exception('Billing terms are not defined');
        }
        return $this->terms[array_rand($this->terms)];
    }

    public function getPriceForTerm(Term $term)
    {
        return $this->getTerm($term->period)->getTotalPrice();
    }

    public function toArray()
    {
        return array_map(function (Term $term) {
            return $term->toArray();
        }, $this->terms);
    }
}
