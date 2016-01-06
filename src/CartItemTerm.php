<?php

namespace Cart;

class CartItemTerm extends CartItem
{
    public function getPrice()
    {
        return $this->data['product']->getPriceForTerm($this->data['term']);
    }

    public function getUnit()
    {
        return $this->data['product']->getUnit();
    }

    public function getTitle()
    {
        return $this->data['product']->title;
    }

    public function getDescription()
    {
        return $this->data['product']->description;
    }

    public function getSave()
    {
        return $this->data['product']->getSaveForTerm($this->data['term']);
    }

    public function getSavePercent()
    {
        return $this->data['product']->getSavePercentForTerm($this->data['term']);
    }

    public function getTerms()
    {
        return $this->data['product']->billing->terms;
    }

    public function getIcannFee()
    {
        if($this->data['product'] instanceof \Hostinger\TaxIcannInterface) {
            return $this->data['product']->getIcannFee($this->data['term']);
        }
        return 0;
    }

    public function getClass()
    {
        return get_class($this->data['product']);
    }

}
