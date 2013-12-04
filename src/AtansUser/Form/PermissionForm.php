<?php
namespace AtansUser\Form;

use AtansUser\Entity\Permission;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Validator\UniqueObject;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class PermissionForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $entities = array(
        'Permission' => 'AtansUser\Entity\Permission',
    );

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('permission-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->setServiceManager($serviceManager);
        $entityManager       = $this->getEntityManager();
        $this->entityManager = $entityManager;

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
        $entityManager = $this->getEntityManager();

        return array(
            'id' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'int'),
                ),
            ),
            'name' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[a-z][a-z0-9_\.]+$/',
                        ),
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'object_manager'    => $entityManager,
                            'object_repository' => $entityManager->getRepository($this->entities['Permission']),
                            'fields' => 'name',
                            'messages' => array(
                                UniqueObject::ERROR_OBJECT_NOT_UNIQUE => 'The permission name already in use',
                            ),
                        ),
                    ),
                ),
            ),
            'description' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ),
        );
    }

    /**
     * Get entityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (! $this->entityManager) {
            $this->setEntityManager($this->getServiceManager()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return RoleForm
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
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
     * @return RoleForm
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
