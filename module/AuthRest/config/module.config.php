<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'AuthRest\Controller\AuthRest' => 'AuthRest\Controller\AuthRestController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'auth-rest' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/v1/auth[/:id]',
                    'constraints' => array(
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'AuthRest\Controller\AuthRest'
                    ),
                ),
            ),
            'login-rest' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/api/v1/login',
                    'defaults' => array(
                        'controller' => 'AuthRest\Controller\AuthRest',
                        'action'     => 'login',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);