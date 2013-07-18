<?php
namespace AtansUser\Form;

use Zend\Form\Form;

class LoginForm extends Form
{

    public function __construct()
    {
        parent::__construct('login-form');

        $this->add(array(
            'name' => 'username',
        ));

        $this->add(array(
            'name' => 'password',
        ));
    }
}