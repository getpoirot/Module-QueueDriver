<?php
namespace Module\QueueDriver\Services;

use Module\Authorization\Services\ContainerAuthenticatorsCapped;
use Poirot\AuthSystem\Authenticate\Authenticator;
use Poirot\AuthSystem\Authenticate\Identifier\aIdentifier;
use Poirot\AuthSystem\Authenticate\Identifier\IdentifierHttpBasicAuth;
use Poirot\AuthSystem\Authenticate\RepoIdentityCredential\IdentityCredentialDigestFile;
use Poirot\Ioc\Container\Service\aServiceContainer;


/**
 * Authenticator Service That Register in Module Authorize as
 * authenticators capped plugin.
 *
 */
class ServiceAuthenticatorFederation
    extends aServiceContainer
{
    protected $name = \Module\QueueDriver\Module::REALM_FEDERATION;
    
    
    /**
     * Create Service
     *
     * @return Authenticator
     */
    function newService()
    {
        $realm      = aIdentifier::DEFAULT_REALM;

        $adapter    = new IdentityCredentialDigestFile;
        $adapter->setPwdFilePath('/to/nowhere'); // currently disable this; work with cli

        // Affect Application Request/Response
        $request    = \IOC::GetIoC()->get('/HttpRequest');
        $response   = \IOC::GetIoC()->get('/HttpResponse');

        $identifier = new IdentifierHttpBasicAuth;
        $identifier
            ->setRequest($request)
            ->setResponse($response)
        ;

        $identifier->setCredentialAdapter($adapter);
        $identifier->setRealm($realm);

        $authenticator = new Authenticator($identifier, $adapter);
        return $authenticator;
    }

    /**
     * @override
     * !! Access Only In Capped Collection; No Nested Containers Here
     *
     * Get Service Container
     *
     * @return ContainerAuthenticatorsCapped
     */
    function services()
    {
        return parent::services();
    }
}
