<?php
namespace Module\QueueDriver\Actions\Worker;

use Module\Foundation\Actions\aAction;
use Poirot\Queue\Payload\BasePayload;


class FeedWorkerAction
    extends aAction
{
    function __invoke($worker_name = null)
    {
        $worker = \Module\QueueDriver\Actions::Worker($worker_name);


        # Add To Queue
        #
        for ($i =1; $i<=10000; $i++) {
            $message = [ 'ver'=>'0.1', 'fun'=> 'print_r', 'args'=> ["<h4> ($i) From High</h4>"] ];
            $worker->queue()->push(new BasePayload($message), 'general');

            usleep( random_int(1, 1000) );
        }


        die;
    }
}
