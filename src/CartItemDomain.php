<?php

namespace Cart;

class CartItemDomain extends CartItemTerm
{
    public function getIcannFee()
    {
        return $this->data['product']->getIcannFee($this->data['term']);
    }
}
