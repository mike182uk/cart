<?php

namespace Cart\Storage;

/**
 * @deprecated use memcached at all times
 * Class MemcacheStore
 * @package Cart\Storage
 */
class MemcacheStore implements Store
{
    /**
     * @var $memcache \Memcache
     */
    protected $memcache;

    public function __construct($host, $port)
    {
        $memcache_obj = new \Memcache;
        $memcache_obj->connect($host, $port);
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
