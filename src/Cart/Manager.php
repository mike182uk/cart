<?php 

namespace Cart;

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
            foreach ($config['carts'] as $cartID => $cartConfig) {
                $cartConfig = array_merge($config['defaults'], $cartConfig); //merge global config with cart specific config
                static::$config['carts'][$cartID] = $cartConfig; //update the config
                static::newCart($cartID, $cartConfig, true, false);
            }

            //set context to first cart in array
            static::$context = key($config['carts']);
        }
    }

    /**
     * Sets the current context if a cart ID is supplied, or gets the current context if no cart ID is supplied
     *
     * @static
     * @param bool|string $cartID If false then the current context is returned, otherwise the current context is set
     * @return string The current context if this is being retrieved
     * @throws Exception\InvalidCartInstanceException
     */
    public static function context($cartID = false)
    {
        if ($cartID) {
            if (isset(static::$instances[$cartID])) {
                static::$context = $cartID;
            }
            else {
                throw new Exception\InvalidCartInstanceException('There is no cart instance with the id: ' . $cartID);
            }
        }

        return static::$context;
    }

    /**
     * Checks to see if there is an instance of a cart with a specific ID
     *
     * @static
     * @param string $cartID The ID of the cart to check for
     * @return bool True if the cart instance exists, false otherwise
     */
    public static function cartExists($cartID)
    {
        return array_key_exists($cartID,static::$instances);
    }

    /**
     * Gets a cart instance. If no $cartID is passed then the cart in the current context
     * is returned. Otherwise requested instance is returned
     *
     * @static
     * @param string|bool $cartID The Id of the cart instance to return
     * @return object The requested cart instance or the current cart instance in context if no $cartID provided
     * @throws Exception\InvalidCartInstanceException
     */
    public static function getCart($cartID = false)
    {
        $cartID or $cartID = static::$context;

        if (static::cartExists($cartID)) {
            return static::$instances[$cartID];
        }
        else {
            throw new Exception\InvalidCartInstanceException('There is no cart instance with the id: ' . $cartID);
        }
    }

    /**
     * Create a new cart instance
     *
     * @static
     * @param string $cartID The ID for the cart instance
     * @param bool|array $config The configuration options associated with this cart
     * @param bool $overwrite If the cart instance already exists should if be overwritten
     * @param bool $switchContext Should the context be switched to this cart instance
     * @return mixed The newly created cart instance
     * @throws Exception\DuplicateCartInstanceException
     */
    public static function newCart($cartID, $config = false, $overwrite = true, $switchContext = true)
    {
        if (!static::cartExists($cartID) or $overwrite) {

            $config or $config = static::getCartConfig($cartID);
            static::$instances[$cartID] = new Cart($cartID, $config);

            /*
             * is there storage options associated with this instance of the cart?
             * if so we need to retrieve any saved data
             */
            if ($config['storage']['driver']) {
                static::restoreCartState($cartID);
            }
            if ($config['storage']['autosave']) {
                //register shutdown function for auto save
                register_shutdown_function(array('\Cart\Manager', 'saveCartState'), $cartID);
            }

            if ($switchContext) {
                static::$context = $cartID;
            }

            return static::$instances[$cartID];
        }
        else {
            throw new Exception\DuplicateCartInstanceException('There is already a cart instance with the id: ' . $cartID);
        }
    }

    /**
     * Destroy a cart instance. If the destroyed cart instance is in the current context, the
     * current context is set to nothing.
     *
     * @static
     * @param bool $cartID The ID of the cart to be destroyed
     * @param bool $clearStorage Should the storage associated with the cart instance be cleared
     */
    public static function destroyCart($cartID = false, $clearStorage = true)
    {
        $cartID or $cartID = static::$context;

        if (static::cartExists($cartID)) {
            unset(static::$instances[$cartID]);

            if ($clearStorage) {
                static::clearCartState($cartID);
            }

            if (static::$context == $cartID) {
                static::$context = '';
            }
        }
    }

    /**
     * Destroy all cart instances associated with the cart manager. Also clears any saved states unless
     * false is passed.
     *
     * @static
     * @param bool $clearStorage Should the storage associated with a cart instance be cleared
     */
    public static function destroyAllCarts($clearStorage = true)
    {
        foreach (static::$instances as $cartID => $cart) {
            static::destroyCart($cartID, $clearStorage);
        }
    }

    /**
     * Get the configuration options specified for a specific cart instance. If not configuration exists
     * for the requested instance, the default cart configuration is returned
     *
     * @static
     * @param string $cartID The ID of the cart instance
     * @return array The cart configuration options
     */
    public static function getCartConfig($cartID = '')
    {
        if (array_key_exists($cartID, static::$config['carts'])) {
            return static::$config['carts'][$cartID];
        }
        else {
            return static::$config['defaults'];
        }
    }

    /**
     * Save data associated with a cart instance to the configured storage method
     *
     * @static
     * @param string $cartID The ID of the cart instance
     */
    public static function saveCartState($cartID)
    {
        $data = serialize(static::$instances[$cartID]->export());
        $driver = static::getCartStorageDriver(static::getCartStorageKey($cartID));
        $driver::save(static::getCartStorageKey($cartID), $data);
    }

    /**
     * Restore data from storage associated with a cart instance
     *
     * @static
     * @param string $cartID The ID of the cart instance
     */
    public static function restoreCartState($cartID)
    {
        $driver = static::getCartStorageDriver($cartID);

        $data = unserialize($driver::restore(static::getCartStorageKey($cartID)));
        static::$instances[$cartID]->import($data);
    }

    /**
     * Clear any saved state associated with a cart instance
     *
     * @static
     * @param string $cartID The ID of the cart instance
     */
    public static function clearCartState($cartID)
    {
        $driver = static::getCartStorageDriver($cartID);
        $driver::clear(static::getCartStorageKey($cartID));
    }

    /**
     * Gets the FQN of the storage implementation associated with a cart instance. Also checks the
     * storage driver is valid
     *
     * @static
     * @param string $cartID The ID of the cart instance
     * @return string The FQN of the storage implementation
     * @throws Exception\InvalidStorageImplementationException
     */
    public static function getCartStorageDriver($cartID)
    {
        $config = static::getCartConfig($cartID);
        $driver = '\Cart\Storage\\' . $config['storage']['driver'];

        //check driver actually exists
        if ( ! class_exists($driver)) {
            throw new Exception\InvalidStorageImplementationException('The class: ' . $driver . ' does has not been loaded.');
        }

        //check driver implements StorageInterface
        $driverInstance = new \ReflectionClass($driver);
        if ( ! $driverInstance->implementsInterface('\Cart\Storage\StorageInterface')) {
            throw new Exception\InvalidStorageImplementationException('The class: ' . $driver . ' does not implement \Cart\Storage\StorageInterface.');
        }

        return $driver;
    }

    /**
     * Gets the storage key associated with a cart instances. Takes into account prefix
     * and suffix set in config
     *
     * @static
     * @param string $cartID The ID of the cart instance
     * @return string The storage key associated with the cart instance
     */
    public static function getCartStorageKey($cartID)
    {
        $config = static::getCartConfig($cartID);

        $storageKey = '';

        if (array_key_exists('storage_key_prefix', $config['storage'])) {
            $storageKey .= $config['storage']['storage_key_prefix'];
        }
        $storageKey .= $cartID;

        if (array_key_exists('storage_key_suffix', $config['storage'])) {
            $storageKey .= $config['storage']['storage_key_suffix'];
        }

        return $storageKey;
    }
}
