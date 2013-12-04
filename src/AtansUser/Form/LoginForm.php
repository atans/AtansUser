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

        $username = new Element\Text('identity');
        $username->setLabel('Username')
                 ->setAttribute('class', 'form-control');
        $this->add($username);

        $password = new Element\Password('credential');
        $password->setLabel('Password')
                 ->setAttribute('class', 'form-control');
        $this->add($password);

        $next = new Element\Hidden('redirect');
        $this->add($next);

        $this->getEventManager()->trigger('init', $this);
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
            'identity' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
            'credential' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
            'redirect' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
        );
    }
}