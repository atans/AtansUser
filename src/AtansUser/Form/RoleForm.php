<?php
namespace AtansUser\Form;

use AtansUser\Entity\Role;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class RoleForm extends ProvidesEventsForm
{
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('role-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $this->setHydrator(new DoctrineHydrator($entityManager))
             ->setObject(new Role());

        $this->add(array(
            'name' => 'id',
        ));

        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'class' => 'span3'
            ),
        ));

        $this->add(array(
            'type' => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
            'name' => 'permissions',
            'options' => array(
                'label_attributes' => array(
                    'class' => 'checkbox'
                ),
                'object_manager' => $entityManager,
                'target_class'   => 'AtansUser\Entity\Permission',
                'property'       => 'name',
                'is_method'      => true,
                'find_method'    => array(
                    'name' => 'findAll',
                ),
            ),
        ));
    }
}
