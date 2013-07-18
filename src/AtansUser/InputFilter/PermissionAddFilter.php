<?php
namespace AtansUser\InputFilter;

use DoctrineModule\Validator\NoObjectExists;
use Zend\ServiceManager\ServiceManager;

class PermissionAddFilter extends PermissionEditFilter
{
    protected $entities = array(
        'Permission' => 'AtansUser\Entity\Permission',
    );

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct($serviceManager);

        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        $noObjectExistsValidator = new NoObjectExists(array(
            'object_repository' => $entityManager->getRepository($this->entities['Permission']),
            'fields' => 'name',
        ));
        $this->get('name')->getValidatorChain()->addValidator($noObjectExistsValidator);
    }
}
