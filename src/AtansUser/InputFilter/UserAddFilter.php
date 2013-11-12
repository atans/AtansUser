<?php
namespace AtansUser\InputFilter;

use DoctrineModule\Validator\NoObjectExists;
use Zend\ServiceManager\ServiceManager;

class UserAddFilter extends UserEditFilter
{
    /**
     * @var array
     */
    protected $entities = array(
        'User' => 'AtansUser\Entity\User',
    );

    function __construct(ServiceManager $serviceManager)
    {
        parent::__construct($serviceManager);

        $entityManager  = $serviceManager->get('doctrine.entitymanager.orm_default');
        $userRepository = $entityManager->getRepository($this->entities['User']);

        $this->add(array(
            'name' => 'id',
            'required' => true,
            'filters' => array(
                array('name' => 'int'),
            ),
        ));

        // TODO username 長度限制
        $this->add(array(
            'name' => 'username',
            'requred' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name' => 'DoctrineModule\Validator\NoObjectExists',
                    'options' => array(
                        'object_repository' => $userRepository,
                        'fields' => array('username'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'required' => true,
            'validators' => array(
                array('name' => 'EmailAddress'),
                array(
                    'name' => 'DoctrineModule\Validator\NoObjectExists',
                    'options' => array(
                        'object_repository' => $userRepository,
                        'fields' => array('email'),
                    ),
                ),
            ),
        ));

        // TODO password regex
        $this->add(array(
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
            'name' => 'userRoles',
            'required' => false,
        ));

        $this->add(array(
            'name' => 'status',
            'required' => true,
        ));
    }
}
