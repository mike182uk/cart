<?php

namespace Cart\Catalog;

use Cart\Arrayable;

class Catalog implements Arrayable, \IteratorAggregate, \JsonSerializable
{
    public $products = array();

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->products);
    }

    public function import(array $array)
    {
        foreach ($array as $id=>$p) {
            $billing = new Billing();
            foreach ($p['billing'] as $t) {
                $term = new Term($t['period']);
                $term->old = $t['old'];
                $term->trial = $t['trial'];
                $term->price = $t['price'];
                $billing->addTerm($term);
            }

            $product = new $p['__class'];
            $product->id = $p['id'];
            $product->title = $p['title'];
            $product->description = $p['description'];
            $product->billing = $billing;
            $this->addProduct($product);
        }
    }

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
