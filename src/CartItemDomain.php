<?php

namespace Cart;

class CartItemDomain extends CartItem
{
    public function getIcannFee()
    {
        return $this->data['product']->getIcannFee($this->data['term']);
    }
}
