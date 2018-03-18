<?php
use Module\CliFoundation\Services\ServiceConsoleRouter;
use Module\QueueDriver\Actions\Worker\FireWorkerCmd;
use Module\QueueDriver\Services\ServiceQueuesContainer;
use Module\QueueDriver\Services\ServiceStorage;

return [
    \Module\QueueDriver\Module::CONF => [

        'worker' => [

            'workers' => [
                'default_worker' => [
                    'max_trades' => 25,
                    'channels' => [
                        'general' => [
                            // Jobs in this queue will executed with DemonShutdown
                            'queue_name' => 'mongodb', // Queue defined in Service Container
                            'weight'     => 10,
                        ],
                    ],
                    'aggregate' => [
                        // Worker Settings
                        'built_in_queue' => 'mongodb',
                    ],

                    'events' => [
                        /*
                        EventHeapOfWorker::EVENT_PAYLOAD_FAILURE => [
                            'listeners' => [
                                [
                                    'listener' => new \Poirot\Ioc\instance(
                                        \Module\Apanaj\Events\Listener\OnWorkerFailureLoggException::class
                                        , [
                                            'logger' => new \Poirot\Ioc\instance('/module/apanaj/services/logger')
                                        ]
                                    ),

                                    'priority' => -100,
                                ]

                            ]
                        ],
                        */
                        /*
                        EventHeapOfWorker::EVENT_PAYLOAD_SUCCEED => [
                            'listeners' => [
                                new \Poirot\Ioc\instance(
                                    \Module\Apanaj\Events\Listener\OnWorkerPayloadReceivedLogg::class
                                    , [
                                        'logger' => new \Poirot\Ioc\instance('/module/apanaj/services/logger')
                                    ]
                                )
                            ],
                        ],
                        */
                    ],
                ],
            ],
        ],

        // Each Queue Can Be Retrieved With Given Name For Usage.
        ServiceQueuesContainer::CONF => [
            // It's a configuration of BuilderContainer [services=>]
            // @see BuildContainer::setServices
            'memory'  => new \Poirot\Queue\Queue\InMemoryQueue(),
            'mongodb' => [
                \Module\QueueDriver\Services\Queue\ServiceQueueMongodb::class,
                'db' => 'mydb', 'client' => 'master', 'collection' => 'queue.app',
            ],
//            'redis'   => [
//                \Module\QueueDriver\Services\Queue\ServiceQueueRedis::class,
//                'scheme' => 'tcp', 'host' => '127.0.0.1', 'port' => '6379', 'password'  => null
//            ]

        ],

        // Storage Used By Worker(s) while running jobs ....
        ServiceStorage::CONF => [
            'instance' => new \Poirot\Ioc\instance(
                \Module\QueueDriver\Services\Storage\ServiceStorageMongodb::class,
                [ 'db' => 'mydb', 'client' => 'master', 'collection' => 'queue.app.storage', ]
            ),
//            'instance' => new \Poirot\Ioc\instance(
//                \Module\QueueDriver\Services\Storage\ServiceStorageRedis::class,
//                [ 'scheme' => 'tcp', 'host' => '127.0.0.1', 'port' => '6379', 'password'  => null ]
//            ),
        ],
    ],


    ## CMD Commands
    #
    ServiceConsoleRouter::CONF => [
        'workers' => [
            'action' => FireWorkerCmd::class,
        ],
    ],

];
