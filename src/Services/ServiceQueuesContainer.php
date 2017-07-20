<?php
namespace Module\QueueDriver\Services;

use Poirot\Application\aSapi;
use Poirot\Ioc\Container\BuildContainer;
use Poirot\Ioc\Container\Service\aServiceContainer;
use Poirot\Std\Struct\DataEntity;


class ServiceQueuesContainer
    extends aServiceContainer
{
    const CONF = 'queues';


    /**
     * Create Service
     *
     * @return ContainerQueuesCapped
     */
    function newService()
    {
        $services = $this->_getConf();
        $services = [
            'services' => $services
        ];

        $builder = new BuildContainer;
        $builder->with( $builder::parseWith($services) );

        $plugins = new ContainerQueuesCapped($builder);
        return $plugins;
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
        $config = $config->get( \Module\QueueDriver\Module::CONF, array() );



        $config = $config[self::CONF];
        return $config;
    }
}
