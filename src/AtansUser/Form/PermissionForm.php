<?php
namespace AtansUser\Form;

use AtansUser\Entity\Permission;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class PermissionForm extends ProvidesEventsForm
{
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('permission-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
        $this->setHydrator(new DoctrineHydrator($entityManager))
            ->setObject(new Permission());

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
            'name' => 'description',
            'attributes' => array(
                'class' => 'span3'
            ),
        ));
    }
}
