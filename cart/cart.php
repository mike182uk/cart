<?php
/**
  Author: Mike!
*/

namespace Cart;

class InvalidCartItemException extends \Exception {}

class InvalidCartConfigException extends \Exception {}

class Cart
{
    /**
     * The items in the cart
     * @var array
     */
    protected $_items = array();

    /**
     * Meta data associated with this cart
     * @var array
     */
    protected $_meta = array();

    /**
     * The id of the cart
     * @var array
     */
    protected $_id = array();

    /**
     * The configuration options associated with this cart
     * @var array
     */
    protected $_config = array();

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Cart constructor. Sets the carts ID. If the cart is not passed an ID, it is automatically assigned one.
     *
     * @param bool|string $id The ID assigned to this cart instance
     * @param array $config The configuration options associated with this cart
     */
    public function __construct($id = false, $config)
    {
        $id or $id = 'cart_' . mktime();
        $this->_id = $id;

        if ( ! is_array($config)) {
            throw new InvalidCartConfigException('Either no configuration options where passed to the cart: ' . $this->_id . ', or the configuration options were not formatted as an array');
        }
        else {
            //do some more checks to make sure correct config items are set etc.
            $this->_config = $config;
        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Add an item to the cart. If the item already exists in the cart, it is updated.
     *
     * @param array $item_data The data associated with the item
     * @return string|bool If the item is added or fields other than the quantity are updated the UID is returned,
     *                     if the item is updated and only the quantity is amended true is returned
     */
    public function add($item_data)
    {
        $uid = $this->_generate_uid($item_data);

        //if item does not have a quantity, set to one
        if ( ! array_key_exists('quantity',$item_data)) {
            $item_data['quantity'] = 1;
        }

        //save timestamp of when this item was added
        if ( ! array_key_exists('added_at',$item_data)) {
            $item_data['added_at'] = mktime();
        }

        //set meta data
        if ( ! array_key_exists('meta',$item_data)) {
            $item_data['meta'] = array();
        }

        //if item already exists, simply update the quantity
        if ($this->exists($uid)) {
            $new_quantity = $this->_items[$uid]->get_quantity() + $item_data['quantity'];
            return $this->update($uid, 'quantity', $new_quantity);
        }
        //otherwise add as a new item
        else {
            $cart_item_config = array(
                'decimal_point' => $this->_config['decimal_point'],
                'decimal_places' => $this->_config['decimal_places'],
                'thousands_separator' => $this->_config['thousands_separator'],
            );
            $this->_items[$uid] = new Cart_Item($item_data, $uid, $cart_item_config);
            return $uid;
        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Update an item in the cart
     *
     * @param $uid The Unique identifier of the item in the cart
     * @param $key The key of the value to be updated
     * @param $value The new value
     * @return bool|mixed If the fields other than the quantity are updated the UID is returned as it is regenerated,
     *                    if the item is updated and only the quantity is amended true is returned
     */
    public function update($uid, $key, $value)
    {
        if ($this->exists($uid)) {
            $item =& $this->_items[$uid];

            //if we are only updating the quantity
            if ($key == 'quantity') {
                if ($value > 0) {
                    $item->set_quantity($value);
                }
                //if the value is less than zero, assume the item is not wanted. maybe the application should handle this logic?
                else {
                    unset($this->_items[$uid]);
                }
                return true;
            }
            //if we are not updating the quantity, we are going to need to update the uid
            else {
                $item_data = $item->export();
                $this->remove($uid);
                return $this->add($item_data);
            }
        }
        else {
            throw new InvalidCartItemException('Cart item does not exist: ' . $uid . ' in the cart instance: ' . $this->_id);
        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Remove an item from the cart
     *
     * @param $uid The Unique identifier of the item in the cart
     * @return bool If the item was removed true is returned, otherwise false is returned
     */
    public function remove($uid)
    {
        if ($this->exists($uid)) {
            unset($this->_items[$uid]);
            return true;
        }
        else {
            return false;
        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Check an item exists in the cart
     *
     * @param $uid The Unique identifier of the item in the cart
     * @return bool Whether the item exists or not
     */
    public function exists($uid)
    {
        return array_key_exists($uid,$this->_items);
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Remove everything out of the cart
     */
    public function clear()
    {
        $this->_items = array();
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Get an item from the cart
     *
     * @param $uid The Unique identifier of the item in the cart
     */
    public function item($uid)
    {
        if ($this->exists($uid)) {
            return $this->_items[$uid];
        }
        else {
            throw new InvalidCartItemException('Cart item does not exist: ' . $uid . ' in the cart instance: ' . $this->_id);
        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Get the contents of the cart
     *
     * @return array All items in the cart
     */
    public function items()
    {
        return $this->_items;
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Get the cart total
     *
     * @param bool $excluding_tax Should the total be returned tax excluded
     * @return float The total for the cart
     */
    public function total($excluding_tax = false)
    {
        $total =  $excluding_tax ?
                  $this->cumulative_price() :
                  $this->cumulative_price() + $this->cumulative_tax();

        return number_format(
            $total,
            $this->_config['decimal_places'],
            $this->_config['decimal_point'],
            $this->_config['thousands_separator']
        );
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Get the cart total tax
     *
     * @return float The total tax for the cart
     */
    public function total_tax()
    {
        return number_format(
            $this->cumulative_tax(),
            $this->_config['decimal_places'],
            $this->_config['decimal_point'],
            $this->_config['thousands_separator']
        );
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Get the number of items in the cart
     *
     * @param bool $unique ignore item quantities
     * @return int The item count
     */
    public function item_count($unique = false)
    {
        return $unique ? count($this->_items) : $this->cumulative_value('quantity');
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Get the total of a certain value that appears on each item in the cart.
     *
     * @param string $key The key of the value
     * @return int The cumulative value for the passed key
     */
    public function cumulative_value($key)
    {
        $counter = 0;
        $items = $this->_items;

        if (count($items) > 0) {
            foreach ($items as $item) {
                if ($key !== 'quantity') {
                    $method = 'get_' . $key;
                    if (is_numeric($item->$method())) {
                        $counter += ($item->$method() * $item->get_quantity());
                    }
                }
                //if the cumulative quantity is required we do not want to multiply by the quantity like above...
                else {
                    $counter += $item->get_quantity();
                }
            }
        }

        return $counter;
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Export the cart
     *
     * @param bool $include_item_uid Should the items UID be included in the exported item data
     * @return array The cart data
     */
    public function export($include_item_uid = false)
    {
        $cart_data = array(
            'items' => array(),
            'meta' => array()
        );

        foreach ($this->_items as $item) {
            $cart_data['items'][] = $item->export($include_item_uid);
        }

        foreach ($this->_meta as $k => $v) {
            $cart_data['meta'][$k] = $v;
        }

        return $cart_data;
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Import a previously saved cart state
     *
     * @param array $cart The data associated with the cart to be imported into the cart
     */
    public function import($cart)
    {
        if (is_array($cart)) {

            //import cart items
            if (array_key_exists('items',$cart) && is_array($cart['items']) && count($cart['items']) > 0) {
                foreach ($cart['items'] as $item) {
                    $this->add($item);
                }
            }

            //import cart meta data
            if (array_key_exists('meta',$cart) && is_array($cart['meta']) && count($cart['meta']) > 0) {
                foreach ($cart['meta'] as $k => $v) {
                    $this->_meta[$k] = $v;
                }
            }

        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Save meta data against cart.
     *
     * @param string $key The key to identify the meta data
     * @param mixed $value The meta data to be saved against the cart
     */
    public function set_meta($key, $value)
    {
        $this->_meta[$key] = $value;
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Retrieve meta data set against a cart
     *
     * @param $key The key to identify the requested meta data
     * @return mixed The meta data retrieved
     */
    public function get_meta($key)
    {
        if (array_key_exists($key, $this->_meta)) {
            return $this->_meta[$key];
        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Remove meta data set against a cart
     *
     * @param $key The key to identify the meta data to be removed
     * @return mixed The meta data retrieved
     */
    public function remove_meta($key)
    {
        unset($this->_meta[$key]);
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Checks if a cart has meta data saved against it. If a key is passed only the presence of
     * meta data with that key is checked for
     *
     * @param bool|string $key The key of the meta data item saved against cart
     * @return bool Whether the cart has meta data saved against it or not
     */
    public function has_meta($key = false)
    {
        if ($key) {
            return array_key_exists($key, $this->_meta);
        }
        else {
            return count($this->_meta) > 0;
        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Used as a catch all for cumulative_* methods. Internally resolves to cumulative_value()
     *
     * @param string $method The name of the method being called
     * @param array $args The arguments passed to the method
     * @return mixed The response of the cumulative value method call
     * @throws \BadMethodCallException
     */
    public function __call($method, $args = array())
    {
        //if method starts with the word cumulative_ assume we are wanting to use the cumulative_value method
        if (substr($method, 0, 11) == 'cumulative_') {
            $key = substr(strtolower($method), 11, strlen($method));
            return $this->cumulative_value($key);
        }
        else {
            throw new \BadMethodCallException('Invalid method: ' . get_called_class() . '::' . $method);
        }
    }

    //-------------------------------------------------------------------------------------------------------------

    /**
     * Generate a unique identifier based on the items data. The array is JSON encoded
     * to get a string representation of the data then md5 hashed to generate a unique
     * value
     *
     * @param array $item_data The items data that will be hashed to generate the UID
     * @return string The UID
     */
    protected function _generate_uid($item_data)
    {
        /*
         * remove keys from the array that are not to be included in the uid hashing process
         * these keys identify supplementary data to the core product data i.e quantity, added_at, meta etc
         */
        $non_uid_compliant = array('quantity', 'added_at', 'meta');
        foreach ($non_uid_compliant as $k) {
            if (array_key_exists($k,$item_data)) {
                unset($item_data[$k]);
            }
        }

        //hash browns...
        return md5(json_encode($item_data));
    }
}