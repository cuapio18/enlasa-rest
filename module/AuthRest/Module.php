<?php
namespace AuthRest;

use Zend\Mvc\MvcEvent;
use Zend\Authentication\Adapter\DbTable as DbAuthAdapter;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager   = $e->getApplication()->getEventManager();

        $serviceManager = $e->getApplication()->getServiceManager();
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'AuthService' => function ($serviceManager) {
                    $adapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $dbAuthAdapter = new DbAuthAdapter ( $adapter, 'iof_users', 'email', 'password' );

                    $select = $dbAuthAdapter->getDbSelect();
                    $select->where('canlogin = 1 AND app_access = 1');
                    	
                    $auth = new AuthenticationService();
                    $auth->setAdapter ( $dbAuthAdapter );
                    return $auth;
                }
            ),
        );
    }
}