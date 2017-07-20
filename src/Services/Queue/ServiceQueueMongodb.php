<?php
namespace Module\QueueDriver\Services\Queue;

use Module\MongoDriver\Actions\MongoDriverAction;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Queue\Queue\MongoQueue;


class ServiceQueueMongodb
    extends aServiceContainer
{
    /**
     * Indicate to allow overriding service
     * with another service
     *
     * @var boolean
     */
    protected $allowOverride = true;

    protected $db         = 'mydb';
    protected $client     = 'master';
    protected $collection = 'queue.app';


    /**
     * Create Service
     *
     * @return mixed
     */
    function newService()
    {
        /** @var MongoDriverAction $driver */
        $driver = \Module\MongoDriver\Actions::Driver();
        $client = $driver->getClient( $this->client );

        $collection = $client->selectCollection($this->db, $this->collection);

        $queue = new MongoQueue($collection);
        return $queue;
    }


    // options:

    /**
     * @param mixed $db
     */
    function setDb($db)
    {
        $this->db = $db;
    }

    /**
     * @param mixed $client
     */
    function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @param mixed $collection
     */
    function setCollection($collection)
    {
        $this->collection = $collection;
    }
}
