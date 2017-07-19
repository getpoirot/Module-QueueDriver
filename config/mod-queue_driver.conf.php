<?php
use Module\QueueDriver\Services\ServiceQueuesContainer;

return [
    \Module\QueueDriver\Module::CONF => [

        'workers' => [
            'default_worker' => [
                'max_trades' => 5,
                'channels' => [
                    'general' => [
                        // Jobs in this queue will executed with DemonShutdown
                        'queue_name' => 'memory',
                        'weight'     => 10,
                    ],
                ],
            ],
        ],

        // Each Queue Can Be Retrieved With Given Name For Usage.
        ServiceQueuesContainer::CONF => [
            // It's a configuration of BuilderContainer [services=>]
            // @see BuildContainer::setServices
            'memory'  => new \Poirot\Queue\Queue\InMemoryQueue(),
            'mongodb' => new \Poirot\Ioc\instance(
                \Module\QueueDriver\Services\Queue\ServiceQueueMongodb::class
                , [
                    'db' => 'mydb', 'client' => 'master', 'collection' => 'queue.app',
                ]
            ),
        ],

    ],
];
