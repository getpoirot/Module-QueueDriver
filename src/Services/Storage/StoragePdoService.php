<?php
namespace Module\QueueDriver\Services\Queue;

use Poirot\Storage\PdoStore;
use Poirot\Ioc\Container\Service\aServiceContainer;


class StoragePdoService
    extends aServiceContainer
{
    /**
     * Indicate to allow overriding service
     * with another service
     *
     * @var boolean
     */
    protected $allowOverride = true;

    protected $dsn;
    protected $user;
    protected $password;
    protected $pdoOptions;
    protected $onInit;


    /**
     * Create Service
     *
     * @return mixed
     */
    function newService()
    {
        ## Prepare PDO Connection
        #
        $conn = new \PDO(
            $this->getDsn()
            , $this->getUser()
            , $this->getPassword()
            , $this->getPdoOptions()
        );


        if ( $init = $this->onInit )
            // exec call invokable initialize setting.
            $init($conn);


        ## Create Queue Service
        #
        $queue = new PdoStore($conn);
        return $queue;
    }


    // options:

    function getDsn()
    {
        return $this->dsn;
    }

    function setDsn($dsn)
    {
        $this->dsn = $dsn;
    }

    function getUser()
    {
        return $this->user;
    }

    function setUser($user)
    {
        $this->user = $user;
    }

    function getPassword()
    {
        return $this->password;
    }

    function setPassword($password)
    {
        $this->password = $password;
    }

    function getPdoOptions()
    {
        return $this->pdoOptions;
    }

    function setPdoOptions(array $pdoOptions = null)
    {
        $this->pdoOptions = $pdoOptions;
    }

    function setOnInit($onInit)
    {
        $this->onInit = $onInit;
    }
}
