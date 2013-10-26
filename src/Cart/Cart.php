<?php

namespace Cart;

use Cart\StoreInterface;
use Cart\CartItem;
use Cart\ArrayableInterface as Arrayable;
use InvalidArgumentException;

class Cart implements Arrayable
{
    /**
     * The cart id.
     *
     * @var string
     */
    protected $id;

    /**
     * Items in the cart.
     *
     * @var array
     */
    protected $items = array();

    /**
     * Cart storage implementation.
     *
     * @var \Cart\StoreInterface
     */
    protected $store;

    /**
     * Create a new cart instance.
     *
     * @param  string               $id
     * @param  \Cart\StoreInterface $store
     * @return void
     */
    public function __construct($id, StoreInterface $store)
    {
        $this->id = $id;
        $this->store = $store;
    }

    /**
     * Retrieve the cart storage impelementation.
     *
     * @return \Cart\StoreInterface
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Retrieve the cart id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retrieve all of the items in the cart.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Add an item to the cart.
     *
     * @param  \Cart\CartItem $cartItem
     * @return void
     */
    public function add(CartItem $cartItem)
    {
        $itemId = $cartItem->getId();

        // if item already exists in the cart, just update the quantity,
        // otherwise add it as a new item
        if ($this->has($itemId)) {
            $existingItem = $this->find($itemId);
            $existingItem->quantity += $cartItem->quantity;
        } else {
            $this->items[] = $cartItem;
        }
    }

    /**
     * Remove an item from the cart.
     *
     * @param  string $itemId
     * @return void
     */
    public function remove($itemId)
    {
        $items =& $this->items;

        foreach ($items as $position => $item) {
            if ($itemId === $item->id) {
                unset($items[$position]);
            }
        }
    }

    /**
     * Update an item in the cart.
     *
     * @param  string $itemId
     * @param  string $key
     * @param  mixed  $value
     * @return string
     */
    public function update($itemId, $key, $value)
    {
        $item = $this->find($itemId);

        if ($item) {
            $item->$key = $value;
        } else {
            throw new InvalidArgumentException(sprintf('Item [%s] does not exist in cart.', $itemId));
        }

        return $item->id;
    }

    /**
     * Retrieve an item from the cart by its id
     *
     * @param  string $itemId
     * @return mixed
     */
    public function get($itemId)
    {
        return $this->find($itemId);
    }

    /**
     * Determine if an item exists in the cart.
     *
     * @param  string  $itemId
     * @return boolean
     */
    public function has($itemId)
    {
        return ! is_null($this->find($itemId));
    }

    /**
     * Find an item in the cart.
     *
     * @param  string $itemId
     * @return mixed
     */
    protected function find($itemId)
    {
        foreach ($this->items as $item) {
            if ($itemId === $item->id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Get the total number of unique items in the cart.
     *
     * @return integer
     */
    public function totalUniqueItems()
    {
        return count($this->items);
    }

    /**
     * Get the total number of items in the cart.
     *
     * @return integer
     */
    public function totalItems()
    {
        return array_sum(array_map(function($item) {
            return $item->quantity;
        }, $this->items));
    }

    /**
     * Get the cart total.
     *
     * @return float
     */
    public function total($includeTax = true)
    {
        return (float) array_sum(array_map(function($item) use ($includeTax) {
            return $item->getTotalPrice($includeTax);
        }, $this->items));
    }

     /**
     * Get the cart tax.
     *
     * @return float
     */
    public function tax()
    {
        return (float) array_sum(array_map(function($item) {
            return $item->getTotalTax();
        }, $this->items));
    }

    /**
     * Remove all items from the cart.
     *
     * @return void
     */
    public function clear()
    {
        $this->items = array();

        $this->store->flush($this->id);
    }

    /**
     * Save the cart state.
     *
     * @return void
     */
    public function save()
    {
        $data = serialize($this->toArray());

        $this->store->put($this->id, $data);
    }

    /**
     * Restore the cart from its saved state.
     *
     * @return void
     */
    public function restore()
    {
        $state = $this->store->get($this->id);

        $data = unserialize($state);

        $this->id = $data['id'];
        $this->items = array();

        foreach ($data['items'] as $itemArr) {
            $this->items[] = new CartItem($itemArr);
        }
    }

    /**
     * Export the cart as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'items' => array_map(function($item) {
                return $item->toArray();
            }, $this->items)
        );
    }
}
