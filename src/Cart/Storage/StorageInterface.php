<?php

namespace Cart\Storage;

/**
 * Base storage interface that must be implemented by all storage methods
 */
interface StorageInterface
{
    /**
     * Restore previously saved state
     *
     * @static
     * @abstract
     * @param string $storageKey String that identifies the data being restored
     */
    public static function restore($storageKey);

    /**
     * Save a cart instances state
     *
     * @static
     * @abstract
     * @param string $storageKey String that identifies the data being saved
     * @param string $data       Data to save
     */
    public static function save($storageKey, $data);

    /**
     * Clears a saved cart instance
     *
     * @static
     * @abstract
     * @param string $storageKey String that identifies the data that is to be cleared
     */
    public static function clear($storageKey);
}
