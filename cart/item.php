<?php 

namespace Cart;

class Cart_Item
{
	/**
	 * Data associated with this item
	 * @var array
	 */
	protected $_data;

	/**
	 * Unique identifier for this item
	 * @var string
	 */
	protected $_uid;

	/**
	 * The configuration options associated with this cart item
	 * @var array
	 */
	protected $_config;

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Item constructor. Set the items meta data + UID. Also stores a reference to the cart it belongs to
	 *
	 * @param array $item_data The meta data associated with this item
	 * @param string $uid The unique identifier for this item
	 * @param array $config The configuration options associated with this cart item
	 */
	public function __construct($item_data, $uid, $config)
	{
		$this->_data = $item_data;
		$this->_uid = $uid;
		$this->_config = $config;
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Get value of a meta data entry
	 *
	 * @param string $key The key associated with the meta data entry
	 * @return mixed The requested meta data
	 */
	public function get($key)
	{
		if (array_key_exists($key,$this->_data)) {
			return $this->_data[$key];
		}
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Set the items quantity
	 *
	 * @param int $quantity The new quantity for the item
	 */
	public function set_quantity($quantity)
	{
		$this->_data['quantity'] = $quantity;
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
     * Used as a catch all for get_* methods. Internally resolves to get()
     *
     * @param string $method The name of the method being called
     * @param array $args The arguments passed to the method
     * @return mixed The response of the get method value method call
     */
	public function __call($method, $args = array())
	{
		//if the method starts with get_ ...
		if (substr($method, 0, 4) == 'get_') {
			$key = substr(strtolower($method), 4, strlen($method));
			return $this->get($key);
		}
		else {
			throw new \BadMethodCallException('Invalid method: ' . get_called_class() . '::' . $method);
		}
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Get price for 1 of this item
	 *
	 * @param bool $excluding_tax Should the single price be returned tax excluded
	 * @return float Price for 1 of item
	 */
	public function single_price($excluding_tax = false)
	{
		$price = $excluding_tax ?
				 $this->get_price() :
				 $this->get_price() + $this->get_tax();

		return number_format(
			$price,
			$this->_config['decimal_places'],
			$this->_config['decimal_point'],
			$this->_config['thousands_separator']
		);
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Get items total price
	 *
	 * @param bool $excluding_tax Should the total price be returned tax excluded
	 * @return float The total cumulative price for this item
	 */
	public function total_price($excluding_tax = false)
	{
		$price = $excluding_tax ?
				 $this->get_price() :
				 $this->get_price() + $this->get_tax();

		return number_format(
			$price * $this->get_quantity(),
			$this->_config['decimal_places'],
			$this->_config['decimal_point'],
			$this->_config['thousands_separator']
		);
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Get tax for 1 of this item
	 *
	 * @return float Tax for 1 of item
	 */
	public function single_tax()
	{
		return number_format(
			$this->get_tax(),
			$this->_config['decimal_places'],
			$this->_config['decimal_point'],
			$this->_config['thousands_separator']
		);
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Get items total tax
	 *
	 * @return float The total cumulative tax for this item
	 */
	public function total_tax()
	{
		return number_format(
			$this->get_tax() * $this->quantity(),
			$this->_config['decimal_places'],
			$this->_config['decimal_point'],
			$this->_config['thousands_separator']
		);

	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Get items quantity
	 *
	 * @return int The quantity of the item
	 */
	public function quantity()
	{
		return $this->_data['quantity'];
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Get items UID
	 *
	 * @return int The UID of the item
	 */
	public function uid()
	{
		return $this->_uid;
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Get an item as an array
	 *
	 * @param bool $include_uid Should the items UID be included in the exported data
	 * @return array The item data as an array
	 */
	public function export($include_uid = false)
	{
		$item_data = $this->_data;
		if ($include_uid) {
			$item_data['uid'] = $this->_uid;	
		}
		return $item_data;
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Save meta data against item. Meta data is not used to generate the item UID.
	 *
	 * @param string $key The key to identify the meta data
	 * @param mixed $value The meta data to be saved against the item
	 */
	public function set_meta($key, $value)
	{
		$this->_data['meta'][$key] = $value;
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Retrieve meta data set against an item
	 *
	 * @param $key The key to identify the requested meta data
	 * @return mixed The meta data retrieved
	 */
	public function get_meta($key)
	{
		if (array_key_exists($key, $this->_data['meta'])) {
			return $this->_data['meta'][$key];
		}
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Remove meta data set against an item
	 *
	 * @param $key The key to identify the meta data to be removed
	 * @return mixed The meta data retrieved
	 */
	public function remove_meta($key)
	{
		unset($this->_data['meta'][$key]);
	}

	//-------------------------------------------------------------------------------------------------------------

	/**
	 * Checks if an item has meta data saved against it. If a key is passed only the presence of
	 * meta data with that key is checked for
	 *
	 * @param bool|string $key The key of the meta data item saved against item
	 * @return bool Whether the item has meta data saved against it or not
	 */
	public function has_meta($key = false)
	{
		if ($key) {
			return array_key_exists($key, $this->_data['meta']);
		}
		else {
			return count($this->_data['meta']) > 0;
		}
	}
}