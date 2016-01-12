<?php

namespace Cart\Catalog;

use Cart\Arrayable;

class Product implements Arrayable
{
    protected $id;

    protected $title;

    protected $description;

    protected $billing;

    protected $group = Group::HOSTING;

    protected $unit = TermLexer::UNIT_MONTH;

    public function __construct()
    {
        $this->billing = new Billing();
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setBilling(Billing $billing)
    {
        $this->billing = $billing;
    }

    public function getBilling()
    {
        return $this->billing;
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
