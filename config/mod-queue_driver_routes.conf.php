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

                    ## GET /q-wrk/demon/fire/default_worker
                    'fireworker' => [
                        'route' => 'RouteSegment',
                        'options' => [
                            // also "validation_code" exists in params and pass through actions as argument
                            'criteria'    => '/fire/:worker_name~\w+~',
                            'match_whole' => true,
                        ],
                        'params'  => [
                            ListenerDispatch::ACTIONS => [
                                \Module\QueueDriver\Actions\Worker\FireWorkerCmd::class,
                            ],
                        ],
                    ],

                    ## GET /q-wrk/demon/feed/default_worker
                    'feed' => [
                        'route' => 'RouteSegment',
                        'options' => [
                            // also "validation_code" exists in params and pass through actions as argument
                            'criteria'    => '/feed/:worker_name~\w+~',
                            'match_whole' => true,
                        ],
                        'params'  => [
                            ListenerDispatch::ACTIONS => [
                                \Module\QueueDriver\Actions\Worker\FeedWorkerAction::class,
                            ],
                        ],
                    ],

                ],

            ],
        ],

    ],
];
