<?php
namespace Module\QueueDriver\Actions\Worker;

use Module\Foundation\Actions\aAction;
use Poirot\Queue\Payload\BasePayload;
use Poirot\Queue\Queue\AggregateQueue;
use Poirot\Queue\Worker;


class FireWorkerAction
    extends aAction
{
    function __invoke($worker_id = null)
    {
        header("Connection: close");

        ob_start();

        echo getmypid();

        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush(); // Strange behaviour, will not work
        flush();

        session_write_close();



        $queue = \Module\QueueDriver\Services::Queues()->get('mongodb');


# Build Aggregate Queue
#
        $qAggregate = new AggregateQueue([
            'threads_high' => [ $queue, 9 ],
            'threads'      => [ $queue, 2 ],
            'will_failed'  => [ $queue, 2 ],
        ]);

        function will_failed($arg)
        {
            static $failed = [];
            if (!isset($failed[$arg])) {
                echo date('H:i:s').' > '.$arg.' Will Failed; and retry again.'.'<br/>';
                $failed[$arg] = true;
                throw new \Exception();

            } else {
                echo date('H:i:s').' > '.$arg.' Recovering Failed.'.'<br/>';
            }

        }

# Add To Queue
#
        for ($i =1; $i<=1000; $i++) {
            $message = [ 'ver'=>'0.1', 'fun'=> 'print_r', 'args'=> ["<h4> ($i) From High</h4>"] ];
            $qAggregate->push(new BasePayload($message), 'threads_high');
        }

# Add To Queue
#
        for ($i =1; $i<=1000; $i++) {
            $message = [ 'ver'=>'0.1', 'fun'=> 'print_r', 'args'=> ["<h5> ($i) Normal</h5>"] ];
            $qAggregate->push(new BasePayload($message), 'threads');
        }

# Add To Queue
#
        for ($i =1; $i<=100; $i++) {
            $message = [ 'ver'=>'0.1', 'fun'=> '\Module\QueueDriver\Actions\Worker\will_failed', 'args' => [ random_int(1, 100) ] ];
            $qAggregate->push(new BasePayload($message), 'will_failed');
        }

        $worker = new Worker('my_worker', $qAggregate, [
            'built_in_queue' => $queue,
        ]);

        $worker->go();

        die;
    }
}
