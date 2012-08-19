<?php

namespace Cart;

class InvalidCartInstanceException extends \Exception {}

class DuplicateCartInstanceException extends \Exception {}

class InvalidStorageImplementationException extends \Exception {}

class Manager
{
    /**
     * Available cart instances
     * @var array
     */
    protected static $instances = array();

    /**
     * The ID of the current cart in context
     * @var string
     */
    protected static $context = '';

    /**
     * The configuration options associated with the carts in the cart manager / cart manager itself
     * @var array
     */
    protected static $config = '';

    /**
     * Initializes the cart manager. Loads in the config and instantiates any carts declared in the config file.
     *
     * @static
     * @param array $config The configuration options associated with this cart manager
     */
    public static function init($config)
    {
        //cache passed config options
        static::$config = $config;

        //if there are carts defined in the config
        if (count($config['carts']) > 0) {
            foreach ($config['carts'] as $cart_id => $cart_config) {
                $cart_config = array_merge($config['defaults'], $cart_config); //merge global config with cart specific config
                static::$config['carts'][$cart_id] = $cart_config; //update the config
                static::new_cart_instance($cart_id, $cart_config, true, false);
            }

            //set context to first cart in array
            static::$context = key($config['carts']);
        }
    }

    /**
     * Sets the current context if a cart ID is supplied, or gets the current context if no cart ID is supplied
     *
     * @static
     * @param bool|string $cart_id If false then the current context is returned, otherwise the current context is set
     * @return string The current context if this is being retrieved
     * @throws InvalidCartInstanceException
     */
    public static function context($cart_id = false)
    {
        if ($cart_id) {
            if (isset(static::$instances[$cart_id])) {
                static::$context = $cart_id;
            }
            else {
                throw new InvalidCartInstanceException('There is no cart instance with the id: ' . $cart_id);
            }
        }

        return static::$context;

    }

    /**
     * Checks to see if there is an instance of a cart with a specific ID
     *
     * @static
     * @param string $cart_id The ID of the cart to check for
     * @return bool True if the cart instance exists, false otherwise
     */
    public static function cart_instance_available($cart_id)
    {
        return array_key_exists($cart_id,static::$instances);
    }

    /**
     * Gets a cart instance. If no $cart_id is passed then the cart in the current context
     * is returned. Otherwise requested instance is returned
     *
     * @static
     * @param string|bool $cart_id The Id of the cart instance to return
     * @return object The requested cart instance or the current cart instance in context if no $cart_id provided
     * @throws InvalidCartInstanceException
     */
    public static function get_cart_instance($cart_id = false)
    {
        $cart_id or $cart_id = static::$context;
        if (static::cart_instance_available($cart_id)) {
            return static::$instances[$cart_id];
        }
        else {
            throw new InvalidCartInstanceException('There is no cart instance with the id: ' . $cart_id);
        }
    }

    /**
     * Create a new cart instance
     *
     * @static
     * @param string $cart_id The ID for the cart instance
     * @param bool|array $cart_config The configuration options associated with this cart
     * @param bool $overwrite If the cart instance already exists should if be overwritten
     * @param bool $switch_context Should the context be switched to this cart instance
     * @return mixed The newly created cart instance
     * @throws DuplicateCartInstanceException
     */
    public static function new_cart_instance($cart_id, $cart_config = false, $overwrite = true, $switch_context = true)
    {
        if (!static::cart_instance_available($cart_id) or $overwrite) {
            $cart_config or $cart_config = static::get_cart_config($cart_id);
            static::$instances[$cart_id] = new Cart($cart_id, $cart_config);

            /*
             * is there storage options associated with this instance of the cart?
             * if so we need to retrieve any saved data
             */
            if ($cart_config['storage']['driver']) {
                static::restore_state($cart_id);
            }
            if ($cart_config['storage']['autosave']) {
                //register shutdown function for auto save
                register_shutdown_function(array('\Cart\Manager', 'save_state'), $cart_id);
            }

            if ($switch_context) {
                static::$context = $cart_id;
            }

            return static::$instances[$cart_id];
        }
        else {
            throw new DuplicateCartInstanceException('There is already a cart instance with the id: ' . $cart_id);
        }
    }

    /**
     * Destroy a cart instance. If the destroyed cart instance is in the current context, the
     * current context is set to nothing.
     *
     * @static
     * @param bool $cart_id The ID of the cart to be destroyed
     * @param bool $clear_storage Should the storage associated with the cart instance be cleared
     */
    public static function destroy_instance($cart_id = false, $clear_storage = true)
    {
        $cart_id or $cart_id = static::$context;
        if (static::cart_instance_available($cart_id)) {
            unset(static::$instances[$cart_id]);

            if ($clear_storage) {
                static::clear_state($cart_id);
            }

            if (static::$context == $cart_id) {
                static::$context = '';
            }
        }
    }

    /**
     * Destroy all cart instances associated with the cart manager. Also clears any saved states unless
     * false is passed.
     *
     * @static
     * @param bool $clear_storage Should the storage associated with a cart instance be cleared
     */
    public static function destroy_all_instances($clear_storage = true)
    {
        foreach (static::$instances as $cart_id => $cart) {
            static::destroy_instance($cart_id, $clear_storage);
        }
    }

    /**
     * Get the configuration options specified for a specific cart instance. If not configuration exists
     * for the requested instance, the default cart configuration is returned
     *
     * @static
     * @param string $cart_id The ID of the cart instance
     * @return array The cart configuration options
     */
    public static function get_cart_config($cart_id = '')
    {
        if (array_key_exists($cart_id,static::$config['carts'])) {
            return static::$config['carts'][$cart_id];
        }
        else {
            return static::$config['defaults'];
        }
    }

    /**
     * Save data associated with a cart instance to the configured storage method
     *
     * @static
     * @param string $cart_id The ID of the cart instance
     */
    public static function save_state($cart_id)
    {
        $data = serialize(static::$instances[$cart_id]->export());
        $driver = static::get_storage_driver(static::get_storage_key($cart_id));
        $driver::save(static::get_storage_key($cart_id), $data);
    }

    /**
     * Restore data from storage associated with a cart instance
     *
     * @static
     * @param string $cart_id The ID of the cart instance
     */
    public static function restore_state($cart_id)
    {
        $driver = static::get_storage_driver($cart_id);

        $data = unserialize($driver::restore(static::get_storage_key($cart_id)));
        static::$instances[$cart_id]->import($data);
    }

    /**
     * Clear any saved state associated with a cart instance
     *
     * @static
     * @param string $cart_id The ID of the cart instance
     */
    public static function clear_state($cart_id)
    {
        $driver = static::get_storage_driver($cart_id);
        $driver::clear(static::get_storage_key($cart_id));
    }

    /**
     * Gets the FQN of the storage implementation associated with a cart instance. Also checks the
     * storage driver is valid
     *
     * @static
     * @param string $cart_id The ID of the cart instance
     * @return string The FQN of the storage implementation
     * @throws InvalidStorageImplementationException
     */
    public static function get_storage_driver($cart_id)
    {
        $cart_config = static::get_cart_config($cart_id);
        $driver = '\Cart\Storage\\' . ucfirst(strtolower($cart_config['storage']['driver']));

        //check driver actually exists
        if ( ! class_exists($driver)) {
            throw new InvalidStorageImplementationException('The class: ' . $driver . ' does has not been loaded.');
        }

        //check driver implements StorageInterface
        $driver_instance = new \ReflectionClass($driver);
        if ( ! $driver_instance->implementsInterface('\Cart\Storage\StorageInterface')) {
            throw new InvalidStorageImplementationException('The class: ' . $driver . ' does not implement the StorageInterface.');
        }

        return $driver;
    }

    /**
     * Gets the storage key associated with a cart instances. Takes into account prefix
     * and suffix set in config
     *
     * @static
     * @param string $cart_id The ID of the cart instance
     * @return string The storage key associated with the cart instance
     */
    public static function get_storage_key($cart_id)
    {
        $cart_config = static::get_cart_config($cart_id);

        $storage_key = '';

        if (array_key_exists('storage_key_prefix',$cart_config['storage'])) {
            $storage_key .= $cart_config['storage']['storage_key_prefix'];
        }
        $storage_key .= $cart_id;

        if (array_key_exists('storage_key_suffix',$cart_config['storage'])) {
            $storage_key .= $cart_config['storage']['storage_key_suffix'];
        }

        return $storage_key;
    }
}
