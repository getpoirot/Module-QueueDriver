<?php
/**
 *
 * @see \Poirot\Ioc\Container\BuildContainer
 */
return [
    'services' => [
        /** Access Registered Workers */
        'Worker'           => \Module\QueueDriver\Actions\Worker\Worker::class,

        'FireWorkerAction' => \Module\QueueDriver\Actions\Worker\FireWorkerAction::class,
        'FeedWorkerAction' => \Module\QueueDriver\Actions\Worker\FeedWorkerAction::class,
    ],
];
