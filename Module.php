<?php
namespace AtansUser;

use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    /**
     * Translator name space
     */
    const TRANSLATOR_TEXT_DOMAIN = __NAMESPACE__;

    public function onBootstrap(MvcEvent $e)
    {
        // Add translation file if default translator was defined
        if ($translator = AbstractValidator::getDefaultTranslator()) {
            $translator->addTranslationFilePattern(
                'phpArray',
                __DIR__ . '/language/Zend_Validate',
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
                'atansuser_change_email_form'        => 'AtansUser\Form\ChangeEmailForm',
                'atansuser_change_password_form'     => 'AtansUser\Form\ChangePasswordForm',
                'atansuser_login_form'               => 'AtansUser\Form\LoginForm',
                'atansuser_permission_search_form'   => 'AtansUser\Form\PermissionSearchForm',
                'atansuser_role_search_form'         => 'AtansUser\Form\RoleSearchForm',
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
                'atansuser_register_form' => function ($sm) {
                    return new Form\RegisterForm($sm);
                },
                'atansuser_permission_form' => function ($sm) {
                    return new Form\PermissionForm($sm);
                },
                'atansuser_role_form' => function ($sm) {
                    return new Form\RoleForm($sm);
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
                        Entity\User::STATUS_ACTIVE   => $translator->translate('Active', self::TRANSLATOR_TEXT_DOMAIN),
                        Entity\User::STATUS_INACTIVE => $translator->translate('Inactive', self::TRANSLATOR_TEXT_DOMAIN),
                        Entity\User::STATUS_DELETED  => $translator->translate('Deleted', self::TRANSLATOR_TEXT_DOMAIN),
                    );
                 },
            ),
        );
    }
}
