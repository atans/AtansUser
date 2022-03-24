<?php
namespace AtansUser;

return array(
    'doctrine' => array(
        'authentication' => array(
            'orm_default' => array(
                'object_manager'      => 'doctrine.entitymanager.orm_default',
                'identity_class'      => 'AtansUser\Entity\User',
                'identity_property'   => 'username',
                'credential_property' => 'password',
            ),
        ),
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ .'\Entity' => __NAMESPACE__ . '_driver',
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'atansuser' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/user',
                    'defaults' => array(
                        'controller' => Controller\UserController::class,
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
                                'action' => 'login',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'action' => 'logout',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/register',
                            'defaults' => array(
                                'action' => 'register',
                            ),
                        ),
                    ),
                    'change-email' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/change-email',
                            'defaults' => array(
                                'action' => 'change-email',
                            ),
                        ),
                    ),
                    'change-password' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/change-password',
                            'defaults' => array(
                                'action' => 'change-password',
                            ),
                        ),
                    ),
                ),
            ),
            'zfcadmin' => array(
                'child_routes' => array(
                    'user' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/user',
                            'defaults' => array(
                                'controller' => Controller\UserAdminController::class,
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/add',
                                    'defaults' => array(
                                        'action' => 'add',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/edit/:id',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'edit',
                                    ),
                                ),
                            ),
                            'delete' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => '/delete/:id',
                                    'constraints' => array(
                                        'id' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'delete',
                                    ),
                                ),
                            ),
                            'permission' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/permission',
                                    'defaults' => array(
                                        'controller' => Controller\PermissionAdminController::class,
                                        'action' => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'add' => array(
                                        'type' => 'literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/edit/:id',
                                            'constraints' => array(
                                                'id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/delete/:id',
                                            'constraints' => array(
                                                'id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                            'role' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/role',
                                    'defaults' => array(
                                        'controller' => Controller\RoleAdminController::class,
                                        'action'     => 'index',
                                    ),
                                ),
                                'may_terminate' => true,
                                'child_routes' => array(
                                    'add' => array(
                                        'type' => 'literal',
                                        'options' => array(
                                            'route' => '/add',
                                            'defaults' => array(
                                                'action' => 'add',
                                            ),
                                        ),
                                    ),
                                    'edit' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/edit/:id',
                                            'constraints' => array(
                                                'id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'action' => 'edit',
                                            ),
                                        ),
                                    ),
                                    'delete' => array(
                                        'type' => 'segment',
                                        'options' => array(
                                            'route' => '/delete/:id',
                                            'constraints' => array(
                                                'id' => '[0-9]+',
                                            ),
                                            'defaults' => array(
                                                'action' => 'delete',
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'zfcrbac' => array(
        'firewalls' => array(
            'ZfcRbac\Firewall\Route' => array(
                array('route' => 'admin/*', 'roles' => 'admin'),
            ),
        ),
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type'        => 'gettext',
                'base_dir'    => __DIR__ . '/../language',
                'pattern'     => '%s.mo',
                'text_domain' => __NAMESPACE__,
            ),
        ),
    ),
    'controllers' => array(
        'factories' => array(
            Controller\PermissionAdminController::class => Controller\PermissionAdminControllerFactory::class,
            Controller\RoleAdminController::class       => Controller\RoleAdminControllerFactory::class,
            Controller\UserAdminController::class       => Controller\UserAdminControllerFactory::class,
            Controller\UserController::class            => Controller\UserControllerFactory::class,
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'atans-user' => __DIR__ . '/../view',
        ),
    ),
);