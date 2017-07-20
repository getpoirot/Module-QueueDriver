<?php
namespace Module\QueueDriver\Actions\Worker;

use Module\Foundation\Actions\aAction;
use Poirot\Queue\Interfaces\iQueueDriver;
use Poirot\Queue\Payload\BasePayload;
use Poirot\Queue\Queue\AggregateQueue;
use Poirot\Queue\Worker;


class FireWorkerAction
    extends aAction
{
    function __invoke($worker_id = null)
    {
        # Send Headers To Client And Keep Running
        #
        $this->_sendHeaders();


        $queue = \Module\QueueDriver\Services::Queues()->get('mongodb');

        $this->_run($queue);


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

        die('>_');
    }


    // ..

    /**
     * Send Headers To Client
     *
     */
    private function _sendHeaders()
    {
        header("Content-Type: text/plain");
        header("Connection: close");

        ob_start();

        echo 'Demon Start, Process #'.getmypid();

        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush(); // Strange behaviour, will not work
        flush();

        // Script Running So Long!
        ignore_user_abort(true);
        session_write_close();
    }

    private function _run(iQueueDriver $queue)
    {
        # Build Aggregate Queue
        #
        $qAggregate = new AggregateQueue([
            'threads_high' => [ $queue, 9 ],
            'threads'      => [ $queue, 2 ],
            'will_failed'  => [ $queue, 2 ],
        ]);

        register_shutdown_function(function() use ($queue, $qAggregate) {
            $worker = new Worker('my_worker', $qAggregate, [
                'built_in_queue' => $queue,
            ]);

            $worker->go();
        });
    }
}
