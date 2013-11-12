<?php
namespace AtansUser\Form;

use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use ZfcBase\Form\ProvidesEventsForm;

class LoginForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('login-form');

        $username = new Element\Text('username');
        $username->setLabel('Username')
                 ->setAttribute('class', 'form-control');
        $this->add($username);

        $password = new Element\Password('password');
        $password->setLabel('Password')
                 ->setAttribute('class', 'form-control');
        $this->add($password);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'username' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
            'password' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
        );
    }
}