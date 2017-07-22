<?php
use Module\QueueDriver\Services;

return [
    'implementations' => [
        'Queues' => Services\ContainerQueuesCapped::class,
    ],
    'services' => [
        // Services Used By QueueDriver

        'Queues'  => Services\ServiceQueuesContainer::class,

        // Keep track of information provided by queues
        'Storage' => Services\ServiceStorage::class,
    ],
];
