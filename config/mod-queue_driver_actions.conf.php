<?php
/** @see \Poirot\Ioc\Container\BuildContainer */

return [
    'services' => [
        /** Access Registered Workers */
        'Worker'           => \Module\QueueDriver\Actions\Worker\Worker::class,
    ],
];
