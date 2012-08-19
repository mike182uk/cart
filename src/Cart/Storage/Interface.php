<?php

namespace Cart\Storage;

/**
 * The base storage interface that must be implemented by all storage methods.
 */
interface StorageInterface
{
    /**
     * Restore previously saved state
     *
     * @static
     * @abstract
     * @param string $storage_key The string that identifies the data being restored
     */
    public static function restore($storage_key);

    /**
     * Save a cart instances state
     *
     * @static
     * @abstract
     * @param string $storage_key The string that identifies the data being saved
     * @param string $data The data saved cart state
     */
    public static function save($storage_key, $data);

    /**
     * Clears a saved cart instance
     *
     * @static
     * @abstract
     * @param string $storage_key The string that identifies the data that is to be cleared
     */
    public static function clear($storage_key);

}