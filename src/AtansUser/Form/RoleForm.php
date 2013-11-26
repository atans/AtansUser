<?php
namespace AtansUser\Form;

use AtansUser\Entity\Role;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Form\Element\ObjectSelect;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Validator\UniqueObject;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class RoleForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    const TRANSLATOR_TEXT_DOMAIN = 'AtansUser';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $entities = array(
        'Role' => 'AtansUser\Entity\Role',
    );

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('role-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->setServiceManager($serviceManager);
        $translator = $serviceManager->get('Translator');

        $entityManager = $this->getEntityManager();
        $this->setHydrator(new DoctrineHydrator($entityManager))
             ->setObject(new Role());

        $id = new Element\Hidden('id');
        $this->add($id);

        $name = new Element\Text('name');
        $name->setLabel('Role name')
             ->setAttribute('class', 'form-control');
        $this->add($name);

        $parent = new ObjectSelect('parent');
        $parent->setLabel("Parent role");
        $parent->setAttribute('class', 'form-control');
        $parent->setOptions(array(
            'empty_option'   => sprintf('== %s ==', $translator->translate('None', static::TRANSLATOR_TEXT_DOMAIN)),
            'object_manager' => $entityManager,
            'target_class'   => 'AtansUser\Entity\Role',
            'property'       => 'name',

        ));
        $this->add($parent);

        $permissions = new ObjectMultiCheckbox('permissions');
        $permissions->setLabel('Permissions')
                    ->setLabelAttributes(array(
                        'class' => 'checkbox-inline'
                    ))
                    ->setOptions(array(
                        'use_hidden_element' => true,
                        'object_manager'     => $entityManager,
                        'target_class'       => 'AtansUser\Entity\Permission',
                        'property'           => 'name',
                        'is_method'          => true,
                        'find_method'        => array(
                            'name' => 'findAll',
                        ),
                    ));

        $this->add($permissions);
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
                    array('name' => 'Int'),
                ),
            ),
            'name' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'Alnum'),
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'object_manager'    => $entityManager,
                            'object_repository' => $entityManager->getRepository($this->entities['Role']),
                            'fields'            => 'name',
                            'messages' => array(
                                UniqueObject::ERROR_OBJECT_NOT_UNIQUE => 'The role name already in use',
                            ),
                        ),
                    ),
                ),
            ),
            'parent' => array(
                'required' => false,
            ),
            'permissions' => array(
                'required' => false,
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
