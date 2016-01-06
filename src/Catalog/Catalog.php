<?php

namespace Cart\Catalog;

use Cart\Arrayable;

class Catalog implements Arrayable
{
    public $products = array();

    public function getProduct($id)
    {
        if (!isset($this->products[$id])) {
            throw new \InvalidArgumentException('Product not found in catalog');
        }
        return $this->products[$id];
    }

    public function getCartItem(Product $product)
    {
        $type = str_replace('Cart\Catalog\Product', '', get_class($product));
        $itemClass = "Cart\\CartItem";
        if (class_exists($itemClass . $type)) {
            $itemClass .= $type;
        }

        $item = new $itemClass();
        $item->term = $product->getRandomTerm();
        $item->product = $product;

        return $item;
    }

    public function addProduct(Product $product)
    {
        $this->products[$product->getId()] = $product;
    }

    public function toArray()
    {
        return array_map(function (Product $product) {
            return $product->toArray();
        }, $this->products);
    }
}
