<?php
namespace AtansUser;

class Module {

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
            'factories' => array(
                'atansuser_module_options' => function ($sm) {
                    $config = $sm->get('config');
                    return new Options\ModuleOptions(isset($config['atansuser']) ? $config['atansuser'] : array());
                },
                'atansuser_login_form' => function ($sm) {
                    return new Form\LoginForm();
                },
                'atansuser_permission_add_form' => function ($sm) {
                    $form = new Form\PermissionForm($sm);
                    $form->setInputFilter(new InputFilter\PermissionAddFilter($sm));
                    return $form;
                },
                'atansuser_permission_edit_form' => function ($sm) {
                    $form = new Form\PermissionForm($sm);
                    $form->setInputFilter(new InputFilter\PermissionEditFilter($sm));
                    return $form;
                },
                'atansuser_role_add_form' => function ($sm) {
                    $form = new Form\RoleForm($sm);
                    $form->setInputFilter(new InputFilter\RoleAddFilter($sm));
                    return $form;
                },
                'atansuser_role_edit_form' => function ($sm) {
                    $form = new Form\RoleForm($sm);
                    $form->setInputFilter(new InputFilter\RoleEditFilter($sm));
                    return $form;
                },
                'atansuser_user_add_form' => function ($sm) {
                    $form = new Form\UserAddForm($sm);
                    $form->setInputFIlter(new InputFilter\UserAddFilter($sm));
                    return $form;
                },
                'atansuser_user_edit_form' => function ($sm) {
                    $form = new Form\UserEditForm($sm);
                    $form->setInputFIlter(new InputFilter\UserEditFilter($sm));
                    return $form;
                },
                'Zend\Authentication\AuthenticationService' => function ($sm) {
                    return $sm->get('doctrine.authenticationservice.orm_default');
                },
            ),
        );
    }
}
