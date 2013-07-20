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
    'navigation' => array(
        'admin' => array(
             array(
                'label' => '用戶',
                'route' => 'zfcadmin/user',
            ),
            array(
                'label' => '權限',
                'route' => 'zfcadmin/permission',
            ),
            array(
                'label' => '角色',
                'route' => 'zfcadmin/role',
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
                'login' => array(
                    'type' => 'segment',
                    'options' => array(
                        'route' => 'login[/]',
                        'defaults' => array(
                            'action' => 'login',
                        ),
                    ),
                ),
            ),
            'zfcadmin' => array(
                'child_routes' => array(
                    'login' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/login[/]',
                            'defaults' => array(
                                'controller' => 'AtansUser\Controller\UserAdmin',
                                'action' => 'login',
                            ),
                        ),
                    ),
                    'user' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/user[/]',
                            'defaults' => array(
                                'controller' => 'AtansUser\Controller\UserAdmin',
                                'action' => 'index',
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
                    'permission' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => 'permission[/]',
                            'defaults' => array(
                                'controller' => 'AtansUser\Controller\PermissionAdmin',
                                'action' => 'index',
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
                                'controller' => 'AtansUser\Controller\RoleAdmin',
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
            'AtansUser\Controller\PermissionAdmin' => 'AtansUser\Controller\PermissionAdminController',
            'AtansUser\Controller\RoleAdmin'       => 'AtansUser\Controller\RoleAdminController',
            'AtansUser\Controller\User'            => 'AtansUser\Controller\UserController',
            'AtansUser\Controller\UserAdmin'       => 'AtansUser\Controller\UserAdminController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'atans-user' => __DIR__ . '/../view',
        ),
    ),
);