<?php
namespace AtansUser\Form;

use Zend\InputFilter\InputFilterProviderInterface;
use ZfcBase\Form\ProvidesEventsForm;

class LoginForm extends ProvidesEventsForm implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('login-form');

        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'class' => 'form-control',
            ),
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