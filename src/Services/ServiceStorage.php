<?php
namespace Module\QueueDriver\Services;

use Poirot\Application\aSapi;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;
use Poirot\Storage\Interfaces\iDataStore;


class ServiceStorage
    extends aServiceContainer
{
    const CONF = 'storage';


    /**
     * Create Service
     *
     * @return iDataStore
     */
    function newService()
    {
        // TODO better instantiate Object

        $conf = $this->_getConf();
        $storage = $conf['instance'];

        return $storage;
    }


    // ..

    /**
     * Get Config Values
     *
     * @return mixed|null
     * @throws \Exception
     */
    protected function _getConf()
    {
        // retrieve and cache config
        $services = $this->services();

        /** @var aSapi $config */
        $config = $services->get('/sapi');
        $config = $config->config();
        /** @var DataEntity $config */
        $config = $config->get(\Module\QueueDriver\Module::CONF, []);
        $config = $config[self::CONF];

        return $config;
    }
}
