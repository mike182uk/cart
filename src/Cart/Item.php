<?php

namespace Cart;

class Item
{
    /**
     * Data associated with this item
     *
     * @var array
     */
    protected $data;

    /**
     * Unique identifier for this item
     *
     * @var string
     */
    protected $uid;

    /**
     * The configuration options associated with this cart item
     *
     * @var array
     */
    protected $config;

    /**
     * Item constructor. Set the items meta data, UID and config options
     *
     * @param array  $itemData Meta data associated with this item
     * @param string $uid      Unique identifier for this item
     * @param array  $config   Configuration options associated with this cart item
     */
    public function __construct($itemData, $uid, $config)
    {
        $this->data   = $itemData;
        $this->uid    = $uid;
        $this->config = $config;
    }

    /**
     * Get value of a meta data entry
     *
     * @param  string $key Key associated with the meta data entry
     * @return mixed  Requested meta data
     */
    public function get($key)
    {
        return array_key_exists($key,$this->data) ? $this->data[$key] : null;
    }

    /**
     * Set the items quantity
     *
     * @param int $quantity New quantity for the item
     */
    public function setQuantity($quantity)
    {
        $this->data['quantity'] = $quantity;
    }

    /**
     * Get price for 1 of this item
     *
     * @param  bool  $excludingTax Should the single price be returned tax excluded
     * @return float Price for 1 of item
     */
    public function singlePrice($excludingTax = false)
    {
        $price = $excludingTax ?
                 $this->data['price'] :
                 $this->data['price'] + $this->data['tax'];

        return number_format(
            $price,
            $this->config['decimal_places'],
            $this->config['decimal_point'],
            $this->config['thousands_separator']
        );
    }

    /**
     * Get items total price
     *
     * @param  bool  $excludingTax Should the total price be returned tax excluded
     * @return float Total aggregate price for this item
     */
    public function totalPrice($excludingTax = false)
    {
        $price = $excludingTax ?
                 $this->data['price'] :
                 $this->data['price'] + $this->data['tax'];

        return number_format(
            $price * $this->data['quantity'],
            $this->config['decimal_places'],
            $this->config['decimal_point'],
            $this->config['thousands_separator']
        );
    }

    /**
     * Get tax for 1 of this item
     *
     * @return float Tax for 1 of item
     */
    public function singleTax()
    {
        return number_format(
            $this->data['tax'],
            $this->config['decimal_places'],
            $this->config['decimal_point'],
            $this->config['thousands_separator']
        );
    }

    /**
     * Get items total tax
     *
     * @return float Total aggregate tax for this item
     */
    public function totalTax()
    {
        return number_format(
            $this->data['tax'] * $this->quantity(),
            $this->config['decimal_places'],
            $this->config['decimal_point'],
            $this->config['thousands_separator']
        );
    }

    /**
     * Get items quantity
     *
     * @return int Quantity of the item
     */
    public function quantity()
    {
        return $this->data['quantity'];
    }

    /**
     * Get items UID
     *
     * @return int UID of the item
     */
    public function uid()
    {
        return $this->uid;
    }

    /**
     * Get an item as an array
     *
     * @param  bool  $includeUID Should the items UID be included in the exported data
     * @return array Item data as an array
     */
    public function export($includeUID = false)
    {
        $itemData = $this->data;
        if ($includeUID) {
            $itemData['uid'] = $this->uid;
        }

        return $itemData;
    }

    /**
     * Save meta data against item. Meta data is not used to generate the item UID.
     *
     * @param string $key   Key to identify the meta data
     * @param mixed  $value Meta data to be saved against the item
     */
    public function setMeta($key, $value)
    {
        $this->data['meta'][$key] = $value;
    }

    /**
     * Retrieve meta data set against an item
     *
     * @param  string $key Key to identify the requested meta data
     * @return mixed  Item Meta data
     */
    public function getMeta($key)
    {
        return array_key_exists($key, $this->data['meta']) ? $this->data['meta'][$key] : null;
    }

    /**
     * Remove meta data set against an item
     *
     * @param  string $key Key to identify the meta data to be removed
     * @return mixed  Item Meta data
     */
    public function removeMeta($key)
    {
        unset($this->data['meta'][$key]);
    }

    /**
     * Checks if an item has meta data saved against it. If a key is passed only the presence of
     * meta data with that key is checked for
     *
     * @param  bool|string $key Key of the meta data item saved against item
     * @return bool        Whether the item has the meta data saved against it or not
     */
    public function hasMeta($key = false)
    {
        if ($key) {
            return array_key_exists($key, $this->data['meta']);
        } else {
            return count($this->data['meta']) > 0;
        }
    }
}
