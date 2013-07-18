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
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity',
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
                'type' => 'segment',
                'options' => array(
                    'route' => '/user[/]',
                    'defaults' => array(
                        'controller' => 'AtansUser\Controller\User',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'login[/]',
                            'defaults' => array(
                                'action' => 'login',
                            ),
                        ),
                    ),
                    'permission' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'permission[/]',
                            'defaults' => array(
                                'controller' => 'AtansUser\Controller\Permission',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => 'add[/]',
                                    'defaults' => array(
                                        'action' => 'add',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => 'edit/:id[/]',
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
                                    'route' => 'delete/:id[/]',
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
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'role[/]',
                            'defaults' => array(
                                'controller' => 'AtansUser\Controller\Role',
                                'action'     => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'add' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => 'add[/]',
                                    'defaults' => array(
                                        'action' => 'add',
                                    ),
                                ),
                            ),
                            'edit' => array(
                                'type' => 'segment',
                                'options' => array(
                                    'route' => 'edit/:id[/]',
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
                                    'route' => 'delete/:id[/]',
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
    'controllers' => array(
        'invokables' => array(
            'AtansUser\Controller\User'       => 'AtansUser\Controller\UserController',
            'AtansUser\Controller\Permission' => 'AtansUser\Controller\PermissionController',
            'AtansUser\Controller\Role'       => 'AtansUser\Controller\RoleController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'atans-user' => __DIR__ . '/../view',
        ),
    ),
);