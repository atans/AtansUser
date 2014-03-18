<?php
namespace AtansUser\Form;

use AtansUser\Entity\Permission;
use AtansUser\Options\ModuleOptions;
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
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var EntityManager
     */
    protected $objectManager;

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

    /**
     * Initialization
     *
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('permission-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->setServiceManager($serviceManager);

        $this->setHydrator(new DoctrineHydrator($this->getObjectManager()))
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
        $objectManager = $this->getObjectManager();

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
                            'object_manager'    => $objectManager,
                            'object_repository' => $objectManager->getRepository($this->entities['Permission']),
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
     * Get moduleOptions
     *
     * @return ModuleOptions
     */
    public function getModuleOptions()
    {
        if (! $this->moduleOptions instanceof ModuleOptions) {
            $this->setModuleOptions($this->getServiceManager()->get('atansuser_module_options'));
        }
        return $this->moduleOptions;
    }

    /**
     * Set moduleOptions
     *
     * @param  ModuleOptions $moduleOptions
     * @return PermissionForm
     */
    public function setModuleOptions(ModuleOptions $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
        return $this;
    }

    /**
     * Get entityManager
     *
     * @return EntityManager
     */
    public function getObjectManager()
    {
        if (! $this->objectManager) {
            $this->setObjectManager($this->getServiceManager()->get($this->getModuleOptions()->getObjectManagerName()));
        }
        return $this->objectManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $objectManager
     * @return RoleForm
     */
    public function setObjectManager(EntityManager $objectManager)
    {
        $this->objectManager = $objectManager;
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
