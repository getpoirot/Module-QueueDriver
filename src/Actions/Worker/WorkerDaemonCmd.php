<?php
namespace Module\QueueDriver\Actions\Worker;

use Module\CliFoundation\Interfaces\iCommand;


class WorkerDaemonCmd
{
    /** @var \Module\QueueDriver\Actions\Worker\WorkerAction */
    protected $worker;
    protected $pids = [];


    /**
     * FireWorkerCmd constructor.
     *
     * @throws \Exception
     */
    function __construct()
    {
        if (! \Poirot\isCommandLine() )
            throw new \Exception('Worker May Executed On CLI Sapi.');


        ## Init
        #
        $this->_initEnvironment();
        $this->_initSignalHandler();
    }


    /**
     * @param iCommand $command
     * @param string   $worker_name
     *
     * @throws \Exception
     */
    function __invoke($command = null, $worker_name = null)
    {
        if ($command) {
            $worker_name = $command->getArg(0);
            $worker_name = $worker_name->getValue();
        }

        $worker       = \Module\QueueDriver\Actions::Worker($worker_name);
        $this->worker = $worker;


        ## Daemonize
        #
        if ( $pid = pcntl_fork() )
            // We are Parent
            exit;


        while (1)
        {
            if ( count($this->pids) < $this->_getMaxAllowedTrades() ) {
                if (0 === $pid = pcntl_fork() ) {
                    try {
                        $this->_run();
                    } catch (\Exception $e) {
                        throw $e;
                    }

                } else {
                    // We add pids to a global array, so that when we get a kill signal
                    // we tell the kids to flush and exit.
                    $this->pids[] = $pid;
                }
            }


            // Collect any children which have exited on their own. pcntl_waitpid will
            // return the PID that exited or 0 or ERROR
            // WNOHANG means we won't sit here waiting if there's not a child ready
            // for us to reap immediately
            // -1 means any child
            $dead_and_gone = pcntl_waitpid(-1, $status, WNOHANG);
            while ($dead_and_gone > 0) {
                // Remove the gone pid from the array
                unset($this->pids[array_search($dead_and_gone, $this->pids)]);

                // Look for another one
                $dead_and_gone = pcntl_waitpid(-1, $status, WNOHANG);
            }

            sleep(1);
        }
    }

    /**
     * When a signal is sent to the process it'll be handled here
     *
     * @param integer $signal
     *
     * @return void
     */
    function handleSignal($signal)
    {
        switch ($signal)
        {
            case SIGUSR1:
                // kill -10 [pid]
                break;
            case SIGHUP:
                // kill -1 [pid]
                break;
            case SIGINT:
            case SIGTERM:

                break;
        }
    }


    // ..

    private function _run()
    {
        $this->worker->goWait();
    }


    private function _getMaxAllowedTrades()
    {
        $workerName = $this->worker->getWorkerName();

        $allowedThreads = \Module\Foundation\Actions::config(
            \Module\QueueDriver\Module::CONF
        );

        $allowedThreads = $allowedThreads['worker']['workers'][$workerName]['max_trades'];
        return (int) $allowedThreads;
    }

    private function _initEnvironment()
    {
        $errors = [];

        if ( function_exists('pcntl_fork') == false )
            $errors[] = "The PCNTL Extension is not installed";

        if ( version_compare(PHP_VERSION, '5.3.0') < 0 )
            $errors[] = "PHP 5.3 or higher is required";


        if ( count($errors) ) {
            $errors = implode("\n  ", $errors);
            throw new \Exception("Checking Dependencies... Failed:\n  $errors");
        }


        ini_set('display_errors', 0);
    }

    private function _initSignalHandler()
    {
        $signals = [
            // Handled by Core_Daemon:
            SIGTERM, SIGINT, SIGUSR1, SIGHUP, SIGCHLD,

            // Ignored by Core_Daemon -- register callback ON_SIGNAL to listen for them.
            // Some of these are duplicated/aliased, listed here for completeness
            SIGUSR2, SIGCONT, SIGQUIT, SIGILL, SIGTRAP, SIGABRT, SIGIOT, SIGBUS, SIGFPE, SIGSEGV, SIGPIPE, SIGALRM,
            SIGCONT, SIGTSTP, SIGTTIN, SIGTTOU, SIGURG, SIGXCPU, SIGXFSZ, SIGVTALRM, SIGPROF,
            SIGWINCH, SIGIO, SIGSYS, SIGBABY
        ];

        if (defined('SIGPOLL'))     $signals[] = SIGPOLL;
        if (defined('SIGPWR'))      $signals[] = SIGPWR;
        if (defined('SIGSTKFLT'))   $signals[] = SIGSTKFLT;

        foreach(array_unique($signals) as $signal)
            pcntl_signal($signal, array($this, 'handleSignals'));
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
