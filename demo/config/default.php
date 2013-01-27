<?php

return array(

    'defaults' => array(
        /**
         * The decimal character to be used when formatting numbers
         */
        'decimal_point' => '.',

        /**
         * The number of decimal places after a number
         */
        'decimal_places' => 2,

        /**
         * The thousands separator character to be used when formatting numbers
         */
        'thousands_separator' => ',',

        /**
         * Storage configuration options
         */
        'storage' => array(
            /**
             * Should the cart autosave at the end of script execution. If true, need
             * to define storage driver
             */
            'autosave' => true,
            /**
             * The FQN of the class handling the storage implementation
             */
            'driver'=> 'SessionStorage',
            /**
             * This is prepended to the storage identifier for the cart instance
             */
            'storage_key_prefix' => 'cart_',
            /**
             * This is appended to the storage identifier for the cart instance
             */
            'storage_key_suffix' => '_instance'
        )
    ),

    /**
     * Carts to be instantiated when the cart manager is initialized. Each instance
     * will inherit the default values above if not specified
     */
    'carts' => array(
        'Cart-01' => array(), //will inherit all of the above options
        'Cart-02' => array(),
        //'cart3' => array()
    )

);
