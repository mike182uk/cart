<?php

namespace Cart\Catalog;

use Cart\Arrayable;
use Cart\CartItem;

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
                $term->setOld($t['old']);
                $term->setTrial($t['trial']);
                $term->setPrice($t['price']);
                $billing->addTerm($term);
            }

            $product = new $p['__class'];
            $product->setId($p['id']);
            $product->setTitle($p['title']);
            $product->setDescription($p['description']);
            $product->setBilling($billing);
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

    /**
     * @param Product $product
     * @param Term $term
     * @param array $data
     * @return CartItem
     */
    public function getCartItem(Product $product, Term $term = null, $data = array())
    {
        $type = str_replace('Cart\Catalog\Product', '', get_class($product));
        $itemClass = "Cart\\CartItem";
        if (class_exists($itemClass . $type)) {
            $itemClass .= $type;
        }

        if(null == $term) {
            $term = $product->getRandomTerm();
        }

        $item = new $itemClass($data);
        $item->term = $term;
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
