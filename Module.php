<?php
namespace AtansUser;

use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        // Add translation file if default translator was defined
        if ($translator = AbstractValidator::getDefaultTranslator()) {
            $translator->addTranslationFilePattern(
                'phpArray',
                __DIR__ . '/languages/Zend_Validate',
                '%s.php'
            );
        }
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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'atansuser_user_service'             => 'AtansUser\Service\User',
                'atansuser_user_admin_service'       => 'AtansUser\Service\UserAdmin',
                'atansuser_permission_admin_service' => 'AtansUser\Service\PermissionAdmin',
                'atansuser_role_admin_service'       => 'AtansUser\Service\RoleAdmin',
            ),
            'factories' => array(
                'atansuser_module_options' => function ($sm) {
                    $config = $sm->get('config');
                    return new Options\ModuleOptions(isset($config['atansuser']) ? $config['atansuser'] : array());
                },
                'atansuser_change_email_form' => function ($sm) {
                    return new Form\ChangeEmailForm();
                },
                'atansuser_change_password_form' => function ($sm) {
                    return new Form\ChangePasswordForm();
                },
                'atansuser_login_form' => function ($sm) {
                    return new Form\LoginForm();
                },
                'atansuser_register_form' => function ($sm) {
                    return new Form\RegisterForm($sm);
                },
                'atansuser_permission_form' => function ($sm) {
                    return new Form\PermissionForm($sm);
                },
                'atansuser_permission_search_form' => function ($sm) {
                    return new Form\PermissionSearchForm($sm);
                },
                'atansuser_role_form' => function ($sm) {
                    return new Form\RoleForm($sm);
                },
                'atansuser_role_search_form' => function ($sm) {
                    return new Form\RoleSearchForm($sm);
                },
                'atansuser_user_add_form' => function ($sm) {
                    return new Form\UserAddForm($sm);
                },
                'atansuser_user_edit_form' => function ($sm) {
                    return new Form\UserEditForm($sm);
                },
                'atansuser_user_search_form' => function ($sm) {
                    return new Form\UserSearchForm($sm);
                },
                'Zend\Authentication\AuthenticationService' => function ($sm) {
                    return $sm->get('doctrine.authenticationservice.orm_default');
                },
                'atansuser_user_statuses' => function ($sm) {
                    $translator = $sm->get('Translator');

                    return array(
                        Entity\User::STATUS_ACTIVE   => $translator->translate('Active', __NAMESPACE__),
                        Entity\User::STATUS_INACTIVE => $translator->translate('Inactive', __NAMESPACE__),
                        Entity\User::STATUS_DELETED  => $translator->translate('Deleted', __NAMESPACE__),
                    );
                 },
            ),
        );
    }
}
