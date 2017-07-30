<?php
namespace Module\QueueDriver\Services;

use Module\Authorization\Guard\GuardRoute;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\Ioc\Container\Service\aServiceContainer;


class ServiceAuthGuard
    extends aServiceContainer
{
    /**
     * Create Service
     *
     * @return GuardRoute
     */
    function newService()
    {
        /** @var Authenticator $auth */
        $auth  = \Module\Authorization\Actions::Authenticator( \Module\QueueDriver\Module::REALM_FEDERATION );
        $guard = new GuardRoute;
        $guard->setAuthenticator( $auth );
        $guard->setRoutesDenied([
            'main/queue/demon/fireworker',
        ]);

        return $guard;
    }
}
