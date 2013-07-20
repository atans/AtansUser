<?php
namespace AtansUser\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use ZfcBase\Form\ProvidesEventsForm;

class LoginForm extends ProvidesEventsForm implements InputFilterProviderInterface
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

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'name' => array(
                'requred' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
            'password' => array(
                'requred' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
        );
    }
}