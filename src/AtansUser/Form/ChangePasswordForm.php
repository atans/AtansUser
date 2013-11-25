<?php
namespace AtansUser\Form;

use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class ChangePasswordForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('change-password-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $credential = new Element\Password('credential');
        $credential->setLabel('Current password')
                   ->setAttribute('class', 'form-control');
        $this->add($credential);

        $newCredential = new Element\Password('newCredential');
        $newCredential->setLabel('New password')
                      ->setAttribute('class', 'form-control');
        $this->add($newCredential);

        $newCredentialVerify = new Element\Password('newCredentialVerify');
        $newCredentialVerify->setLabel('Verify new password')
                            ->setAttribute('class', 'form-control');
        $this->add($newCredentialVerify);

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
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                ),
            ),
            'newCredential' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                ),
            ),
            'newCredentialVerify' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'identical',
                        'options' => array(
                            'token' => 'newCredential',
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Get serviceManager
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set serviceManager
     *
     * @param  ServiceManager $serviceManager
     * @return ChangePasswordForm
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}