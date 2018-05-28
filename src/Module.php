<?php
namespace Module\QueueDriver
{
    use Poirot\Application\Interfaces\Sapi\iSapiModule;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Application\ModuleManager\Interfaces\iModuleManager;
    use Poirot\Application\Sapi\Module\ContainerForFeatureActions;

    use Poirot\Ioc\Container;
    use Poirot\Ioc\Container\BuildContainer;

    use Poirot\Std\Interfaces\Struct\iDataEntity;


    /**
     * Services:
     *   > Queues
     *     define some queue plugins can accessed by name;
     *
     *     Module\QueueDriver\Services::Queues()->get('memory')
     *
     *   > Storage
     *     storage used by aggregate queue to pick tasks and store some
     *     information about trades and etc.
     *
     *
     * Actions:
     *   > Worker
     *
     *    [code]
     *      $worker = \Module\QueueDriver\Actions::Worker('default_worker');
     *      $worker->queue()->push(new BasePayload([
     *        'command' => UploadPostLargeFile::COMMAND,
     *        'param1'  => 'value1',
     *      ]));
     *    [/code]
     *
     *    @see Worker
     *
     */
    class Module implements iSapiModule
        , Sapi\Module\Feature\iFeatureModuleMergeConfig
        , Sapi\Module\Feature\iFeatureModuleInitModuleManager
        , Sapi\Module\Feature\iFeatureModuleNestActions
        , Sapi\Module\Feature\iFeatureModuleNestServices
    {
        const CONF  = 'mod.queue_driver';
        const REALM_FEDERATION = 'mod.queue_driver.realm';


        /**
         * Register config key/value
         *
         * priority: 1000 D
         *
         * - you may return an array or Traversable
         *   that would be merge with config current data
         *
         * @param iDataEntity $config
         *
         * @return array|\Traversable
         */
        function initConfig(iDataEntity $config)
        {
            return \Poirot\Config\load(__DIR__ . '/../config/mod-queue_driver');
        }

        /**
         * Initialize Module Manager
         *
         * priority: 1000 C
         *
         * @param iModuleManager $moduleManager
         *
         * @return void
         */
        function initModuleManager(iModuleManager $moduleManager)
        {
            // ( ! ) ORDER IS MANDATORY

            if (!$moduleManager->hasLoaded('Authorization'))
                // Authorization Module Is Required.
                $moduleManager->loadModule('Authorization');
        }

        /**
         * Get Nested Module Services
         *
         * it can be used to manipulate other registered services by modules
         * with passed Container instance as argument.
         *
         * priority not that serious
         *
         * @param Container $moduleContainer
         *
         * @return null|array|BuildContainer|\Traversable
         */
        function getServices(Container $moduleContainer = null)
        {
            $conf    = \Poirot\Config\load(__DIR__ . '/../config/mod-queue_driver_services');

            $builder = new BuildContainer;
            $builder->with($builder::parseWith($conf));
            return $builder;
        }

        /**
         * Get Action Services
         *
         * priority: after GrabRegisteredServices
         *
         * - return Array used to Build ModuleActionsContainer
         *
         * @return array|ContainerForFeatureActions|BuildContainer|\Traversable
         */
        function getActions()
        {
            return \Poirot\Config\load(__DIR__ . '/../config/mod-queue_driver_actions');
        }

    }
}


namespace Module\QueueDriver
{
    use Poirot\Storage\Interfaces\iDataStore;
    use Module\QueueDriver\Actions\Worker\WorkerAction;
    use Module\QueueDriver\Services\ContainerQueuesCapped;


    /**
     * @method static ContainerQueuesCapped Queues()
     * @method static iDataStore            Storage()
     */
    class Services extends \IOC
    { }


    /**
     * @method static WorkerAction Worker($worker_name)
     */
    class Actions extends \IOC
    { }
}
