<?php

namespace Cart\Catalog;

use Cart\Arrayable;

class Product implements Arrayable
{
    public $id;

    public $title;

    public $description;

    public $billing;

    protected $group = Group::HOSTING;

    protected $unit = TermLexer::UNIT_MONTH;

    public function __construct()
    {
        $this->billing = new Billing();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getTerm($period)
    {
        return $this->billing->getTerm($period);
    }

    public function getRandomTerm()
    {
        return $this->billing->getRandomTerm();
    }

    public function getPriceForTerm(Term $term)
    {
        return $this->billing->getPriceForTerm($term);
    }

    public function getSaveForTerm(Term $term)
    {
        return $this->billing->getSaveForTerm($term);
    }

    public function getSavePercentForTerm(Term $term)
    {
        return $this->billing->getSavePercentForTerm($term);
    }

    public function toArray()
    {
        return array(
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'billing'       => $this->billing->toArray(),
            '__class'       => get_class($this),
        );
    }
}
