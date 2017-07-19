<?php
namespace Module\QueueDriver
{
    use Module\OAuth2\Services\BuildServices;

    use Poirot\Application\Interfaces\Sapi\iSapiModule;
    use Poirot\Application\Interfaces\Sapi;
    use Poirot\Application\Sapi\Module\ContainerForFeatureActions;

    use Poirot\Ioc\Container;
    use Poirot\Ioc\Container\BuildContainer;

    use Poirot\Router\BuildRouterStack;
    use Poirot\Router\Interfaces\iRouterStack;

    use Poirot\Std\Interfaces\Struct\iDataEntity;


    class Module implements iSapiModule
        , Sapi\Module\Feature\iFeatureModuleMergeConfig
        , Sapi\Module\Feature\iFeatureModuleNestActions
        , Sapi\Module\Feature\iFeatureModuleNestServices
        , Sapi\Module\Feature\iFeatureOnPostLoadModulesGrabServices
    {
        const CONF = 'mod.queue_driver';


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

            $builder = new BuildServices;
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

        /**
         * Resolve to service with name
         *
         * - each argument represent requested service by registered name
         *   if service not available default argument value remains
         * - "services" as argument will retrieve services container itself.
         *
         * ! after all modules loaded
         *
         * @param iRouterStack $router
         *
         * @internal param null $services service names must have default value
         */
        function resolveRegisteredServices($router = null)
        {
            # Register Http Routes:
            if ($router) {
                $routes = include __DIR__ . '/../config/mod-queue_driver_routes.conf.php';
                $buildRoute = new BuildRouterStack();
                $buildRoute->setRoutes($routes);
                $buildRoute->build($router);
            }
        }
    }
}


namespace Module\QueueDriver
{
    use Module\QueueDriver\Services\ContainerQueuesCapped;

    /**
     * @method static ContainerQueuesCapped Queues()
     */
    class Services extends \IOC
    { }
}
