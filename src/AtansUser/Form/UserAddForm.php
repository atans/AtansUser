<?php
namespace AtansUser\Form;

use AtansUser\Entity\User;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Element;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class UserAddForm extends ProvidesEventsForm
{
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('user-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $translator    = $serviceManager->get('translator');
        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $this->setHydrator(new DoctrineObject($entityManager))
             ->setObject(new User());

        $id = new Element\Hidden('id');
        $this->add($id);

        $username = new Element\Text('username');
        $username->setLabel('Username')
                 ->setAttribute('class', 'form-control');
        $this->add($username);

        $email = new Element\Email('email');
        $email->setLabel('Email')
              ->setAttribute('class', 'form-control');
        $this->add($email);

        $password = new Element\Password('password');
        $password->setLabel('Password')
                 ->setAttribute('class', 'form-control');
        $this->add($password);

        $userRoles = new ObjectMultiCheckbox('userRoles');
        $userRoles->setLabel('Roles')
                  ->setLabelAttributes(array(
                      'class' => 'checkbox-inline'
                  ))
                  ->setOptions(array(
                      'object_manager' => $entityManager,
                      'target_class'   => 'AtansUser\Entity\Role',
                      'property'       => 'name',
                      'is_method'      => true,
                      'find_method'    => array(
                          'name' => 'findAll',
                      ),
                  ));
        $this->add($userRoles);

        $status = new Element\Radio('status');
        $status->setLabel('Status')
               ->setLabelAttributes(array(
                   'class' => 'radio-inline',
               ))
               ->setOptions(array(
                   'value_options' => array(
                       User::STATUS_ACTIVE   => $translator->translate('Active'),
                       User::STATUS_INACTIVE => $translator->translate('Inactive'),
                       User::STATUS_DELETED  => $translator->translate('Deleted'),
                   ),
               ));
        $this->add($status);
    }
}
