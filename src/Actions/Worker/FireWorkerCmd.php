<?php
namespace Module\QueueDriver\Actions\Worker;

use Module\CliFoundation\Interfaces\iCommand;


/**
 * > ./poirot workers default_worker &
 *
 */
class FireWorkerCmd
{
    protected $worker;


    /**
     * FireWorkerCmd constructor.
     *
     * @throws \Exception
     */
    function __construct()
    {
        if (! \Poirot\isCommandLine() )
            throw new \Exception('Worker May Executed On CLI Sapi.');


        ini_set('display_errors', 0);
    }


    /**
     * @param iCommand $command
     */
    function __invoke($command = null, $worker_name = null)
    {
        if ($command) {
            $worker_name = $command->getArg(0);
            $worker_name = $worker_name->getValue();
        }


        $worker       = \Module\QueueDriver\Actions::Worker($worker_name);
        $this->worker = $worker;


        # Cleanup Storage From Killed Pids
        #
        $this->_refreshKilledPids($worker);


        # Keep Track Worker In Storage
        #
        $this->_keepTrack($worker);



        $self = $this;
        register_shutdown_function(function () use ($self) {
            $self->__destruct();
        });


        # Check Whether The Maximum Threads Reached?
        #
        if ( false === $this->_isAllowedByMaxThreads($worker) )
            die('No More Threads Allowed.');



        # Send Headers To Client And Keep Running
        #
        $this->_sendHeaders();
        $this->_run($worker);

        die;
    }


    // ..

    /**
     * Send Headers To Client
     *
     */
    private function _sendHeaders()
    {
        echo 'Demon Start, Process #'.getmypid();

        // Script Running So Long!
        ignore_user_abort(true);
    }

    private function _run(\Module\QueueDriver\Actions\Worker\WorkerAction $worker)
    {
        $worker->goWait();
    }


    private function _refreshKilledPids($worker)
    {
        /** @var WorkerAction $worker */

        $storage    = \Module\QueueDriver\Services::Storage();
        $data       = $storage->get($worker->getWorkerName(), []);

        foreach ($data as $workerId => $d) {
            $pid = $d['pid'];
            if (! file_exists("/proc/$pid") )
                // Process is killed, remove from list
                unset($data[$workerId]);
        }

        $storage->set($worker->getWorkerName(), $data);
    }

    private function _keepTrack($worker)
    {
        /** @var WorkerAction $worker */

        $workerName = $worker->getWorkerName();
        $workerID   = $worker->getWorkerID();
        $processID  =  getmypid();

        $storage    = \Module\QueueDriver\Services::Storage();
        if (! $storage->has($workerName) )
            $storage->set($workerName, []);

        $data = $storage->get($workerName);
        $data[$workerID] = [
            'timestamp_created' => time(),
            'pid'               => $processID,
        ];

        $storage->set($workerName, $data);
    }

    private function _isAllowedByMaxThreads($worker)
    {
        /** @var WorkerAction $worker */

        $workerName = $worker->getWorkerName();

        $allowedThreads = \Module\Foundation\Actions::config(
            \Module\QueueDriver\Module::CONF
        );

        $allowedThreads = $allowedThreads['worker']['workers'][$workerName]['max_trades'];

        if ($allowedThreads === null)
            return true;


        # Check Currently Running Threads
        #
        $storage    = \Module\QueueDriver\Services::Storage();
        $data       = $storage->get($workerName, []);

        return ( $allowedThreads >= count($data) );
    }

    function __destruct()
    {
        /** @var WorkerAction $worker */
        if ( null === $worker = $this->worker )
            return;


        $workerName = $worker->getWorkerName();
        $workerID   = $worker->getWorkerID();

        $storage    = \Module\QueueDriver\Services::Storage();
        $data       = $storage->get($workerName);
        unset($data[$workerID]);

        $storage->set($workerName, $data);
    }
}
