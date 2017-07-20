<?php
use Module\HttpFoundation\Events\Listener\ListenerDispatch;

return [
    'queue'  => [

        'route' => 'RouteSegment',
        'options' => [
            'criteria'    => '/q-wrk',
            'match_whole' => false,
        ],

        'routes' => [
            'demon' => [
                'route' => 'RouteSegment',
                'options' => [
                    'criteria'    => '/demon',
                    'match_whole' => false,
                ],

                'routes' => [
                    'fireworker' => [
                        'route' => 'RouteSegment',
                        'options' => [
                            // also "validation_code" exists in params and pass through actions as argument
                            'criteria'    => '/fire/:worker_id~\w+~',
                            'match_whole' => true,
                        ],
                        'params'  => [
                            ListenerDispatch::ACTIONS => [
                                '/module/queueDriver/actions/FireWorkerAction',
                            ],
                        ],
                    ],
                ],

            ],
        ],

    ],
];
