<?php

namespace Cart;

class CartItemTerm extends CartItem
{
    protected $term;

    protected $product;

    public function setTerm($term)
    {
        $this->term = $term;
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function getPrice()
    {
        return $this->product->getPriceForTerm($this->term);
    }

    public function getSave()
    {
        return $this->product->getSaveForTerm($this->term);
    }

    public function getSavePercent()
    {
        return $this->product->getSaveForTerm($this->term);
    }

    public function getTitle()
    {
        return $this->product->title;
    }

    public function getDescription()
    {
        return $this->product->description;
    }
}
