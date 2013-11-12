<?php
namespace AtansUser\Form;

use AtansUser\Entity\Permission;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Element;
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

        $id = new Element\Hidden('id');
        $this->add($id);

        $name = new Element\Text('name');
        $name->setLabel('Permission name')
             ->setAttribute('class', 'form-control');
        $this->add($name);


        $description = new Element\Text('description');
        $description->setLabel('Permission description')
                    ->setAttribute('class', 'form-control');
        $this->add($description);
    }
}
