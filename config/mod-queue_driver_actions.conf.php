<?php
/**
 *
 * @see \Poirot\Ioc\Container\BuildContainer
 */
return [
    'services' => [
        'FireWorkerAction' => \Module\QueueDriver\Actions\Worker\FireWorkerAction::class,
    ],
];
