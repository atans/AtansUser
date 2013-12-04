<?php
namespace AtansUser\Form;

use Zend\Form\Element;
use Zend\ServiceManager\ServiceManager;

class UserEditForm extends UserAddForm
{
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct($serviceManager, 'user-edit-form');

        $this->remove('password');

        $password = new Element\Password('newPassword');
        $password->setLabel('New password')
            ->setAttribute('class', 'form-control');
        $this->add($password);

        $this->getEventManager()->trigger('init', $this);
    }

    public function getInputFilterSpecification()
    {
        $inputFilter = parent::getInputFilterSpecification();
        $inputFilter['newPassword'] = $inputFilter['password'];
        unset($inputFilter['password']);
        $inputFilter['newPassword']['required'] = false;

        return $inputFilter;
    }
}
