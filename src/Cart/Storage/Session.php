<?php

namespace Cart\Storage;

use Cart\Storage\StorageInterface;

class Session implements StorageInterface
{
    public static function init()
    {
        @session_start();
    }

    public static function restore($storageKey)
    {
        static::init();

        return isset($_SESSION[$storageKey]) ? $_SESSION[$storageKey] : null;
    }

    public static function save($storageKey, $data)
    {
        static::init();

        $_SESSION[$storageKey] = $data;
    }

    public static function clear($storageKey)
    {
        static::init();

        unset($_SESSION[$storageKey]);
    }
}
