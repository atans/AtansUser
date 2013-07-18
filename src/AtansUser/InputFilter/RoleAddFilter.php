<?php
namespace AtansUser\InputFilter;

use DoctrineModule\Validator\NoObjectExists;
use Zend\ServiceManager\ServiceManager;

class RoleAddFilter extends RoleEditFilter
{
    protected $entities = array(
        'Role' => 'AtansUser\Entity\Role',
    );

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct($serviceManager);

        $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');

        $noObjectExistsValidator = new NoObjectExists(array(
            'object_repository' => $entityManager->getRepository($this->entities['Role']),
            'fields' => 'name',
        ));
        $this->get('name')->getValidatorChain()->addValidator($noObjectExistsValidator);
    }
}
