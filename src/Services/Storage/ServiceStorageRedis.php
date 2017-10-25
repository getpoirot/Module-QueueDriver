<?php

namespace Module\QueueDriver\Services\Storage;

use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Storage\RedisStore;

class ServiceStorageRedis extends aServiceContainer
{

    protected $scheme               = "tcp";
    protected $host                 = "127.0.0.1";
    protected $port                 = 6379;
    /**
     * @var string $path
     * Path of the UNIX domain socket file used when connecting to Redis using UNIX domain sockets.
     */
    protected $path                 = null;
    /**
     * @var integer $database
     * Accepts a numeric value that is used by Predis to automatically select a logical database with the SELECT command.
     */
    protected $database             = null;
    /**
     * @var string $password
     * Accepts a value used to authenticate with a Redis server protected by password with the AUTH command.
     */
    protected $password             = null;
    protected $async                = false;
    protected $persistent           = false;
    protected $timeout              = 5.0;
    /**
     * @var float $read_write_timeout
     * Timeout (expressed in seconds) used when performing read or write operations on the underlying network resource after which an exception is thrown. The default value actually depends on the underlying platform but usually it is 60 seconds.
     */
    protected $read_write_timeout   = null;
    /**
     * @var string $alias
     * Identifies a connection by providing a mnemonic alias. This is mostly useful with aggregated connections such as client-side sharding (cluster) or master/slave replication.
     */
    protected $alias                = null;
    /**
     * @var integer $weight
     * Specifies a weight used to balance the distribution of keys asymmetrically across multiple servers when using client-side sharding (cluster).
     */
    protected $weight               = null;
    protected $iterable_multibulk   = false;
    protected $throw_errors         = true;


    /**
     * Create Service
     *
     * @return mixed
     */
    function newService()
    {
        $client = new \Predis\Client([
            'scheme'                => $this->scheme,
            'host'                  => $this->host,
            'port'                  => $this->port,
            'path'                  => $this->path,
            'database'              => $this->database,
            'password'              => $this->password,
            'async'                 => $this->async,
            'persistent'            => $this->persistent,
            'timeout'               => $this->timeout,
            'read_write_timeout'    => $this->read_write_timeout,
            'alias'                 => $this->alias,
            'weight'                => $this->weight,
            'iterable_multibulk'    => $this->iterable_multibulk,
            'throw_errors'          => $this->throw_errors
        ]);
        return new RedisStore('queue.app', ['client' => $client]);
    }

    /**
     * @param string $scheme
     * @return ServiceStorageRedis
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @param string $host
     * @return ServiceStorageRedis
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param int $port
     * @return ServiceStorageRedis
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param string $path
     * @return ServiceStorageRedis
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @param int $database
     * @return ServiceStorageRedis
     */
    public function setDatabase($database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @param string $password
     * @return ServiceStorageRedis
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param bool $async
     * @return ServiceStorageRedis
     */
    public function setAsync($async)
    {
        $this->async = $async;
        return $this;
    }

    /**
     * @param bool $persistent
     * @return ServiceStorageRedis
     */
    public function setPersistent($persistent)
    {
        $this->persistent = $persistent;
        return $this;
    }

    /**
     * @param float $timeout
     * @return ServiceStorageRedis
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param float $read_write_timeout
     * @return ServiceStorageRedis
     */
    public function setReadWriteTimeout($read_write_timeout)
    {
        $this->read_write_timeout = $read_write_timeout;
        return $this;
    }

    /**
     * @param string $alias
     * @return ServiceStorageRedis
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @param int $weight
     * @return ServiceStorageRedis
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @param bool $iterable_multibulk
     * @return ServiceStorageRedis
     */
    public function setIterableMultibulk($iterable_multibulk)
    {
        $this->iterable_multibulk = $iterable_multibulk;
        return $this;
    }

    /**
     * @param bool $throw_errors
     * @return ServiceStorageRedis
     */
    public function setThrowErrors($throw_errors)
    {
        $this->throw_errors = $throw_errors;
        return $this;
    }

}