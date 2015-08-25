<?php
namespace AtansUser\Form;

use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use ZfcBase\Form\ProvidesEventsForm;

class ChangeEmailForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct('change-email-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $credential = new Element\Password('credential');
        $credential->setLabel('Current password')
                   ->setAttribute('class', 'form-control');
        $this->add($credential);

        $newEmail = new Element\Email('newEmail');
        $newEmail->setLabel('New email')
                 ->setAttribute('class', 'form-control');
        $this->add($newEmail);

        $newEmailVerify = new Element\Email('newEmailVerify');
        $newEmailVerify->setLabel('Verify new email')
                       ->setAttribute('class', 'form-control');
        $this->add($newEmailVerify);

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
            'credential' => array(
                'required' => true,
            ),
            'newEmail' => array(
                'required' => true,
            ),
            'newEmailVerify' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'identical',
                        'options' => array(
                            'token' => 'newEmail',
                        ),
                    ),
                ),
            ),
        );
    }
}