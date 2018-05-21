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

            /* MongoDB
            'mongodb' => [
                \Module\QueueDriver\Services\Queue\ServiceQueueMongodb::class,
                'db' => 'mydb', 'client' => 'master', 'collection' => 'queue.app',
            ],
            */

            /* Redis
            'redis' => [
                \Module\QueueDriver\Services\Queue\ServiceQueueRedis::class,
                'scheme' => 'tcp', 'host' => '127.0.0.1', 'port' => '6379', 'password'  => null
            ]
            */

            /* Pdo/mySql
            'pdo' => [
                \Module\QueueDriver\Services\Queue\QueuePdoService::class,
                'dsn' => 'mysql:host=localhost;dbname=test',
                'user' => 'root', 'password' => '***',
                'pdo_options' => [
                   PDO::ATTR_PERSISTENT = true,
                   PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
                ],
                'on_init' => function(\PDO $conn) {
                    // set the PDO error mode to exception
                    $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
                    $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET CHARACTER SET 'utf8'");
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $conn->exec("SET NAMES utf8");
                },
            ]
            */
        ],

        // Storage Used By Worker(s) while running jobs ....
        ServiceStorage::CONF => [

            /* MongoDb
            'instance' => new \Poirot\Ioc\instance(
                \Module\QueueDriver\Services\Storage\ServiceStorageMongodb::class,
                [ 'db' => 'mydb', 'client' => 'master', 'collection' => 'queue.app.storage', ]
            ),
            */

            /* Redis
            'instance' => new \Poirot\Ioc\instance(
                \Module\QueueDriver\Services\Storage\ServiceStorageRedis::class,
                [ 'scheme' => 'tcp', 'host' => '127.0.0.1', 'port' => '6379', 'password'  => null ]
            ),
            */

            /*

            */
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
