<?php

namespace Cart\Storage;

/**
 * Use MemcachedStore instead of MemcacheStore in all cases
 *
 * Class MemcachedStore
 * @package Cart\Storage
 */
class MemcachedStore implements Store
{
    /**
     * @var $memcache \Memcache
     */
    protected $memcache;

    public function __construct($host, $port)
    {
        $memcache_obj = new \Memcached;
        $memcache_obj->addServer($host, $port);
        $this->memcache = $memcache_obj;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        return $this->memcache->get($cartId);
    }

    /**
     * {@inheritdoc}
     */
    public function put($cartId, $data)
    {
        return $this->memcache->set($cartId, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function flush($cartId)
    {
        return $this->memcache->delete($cartId);
    }
}
