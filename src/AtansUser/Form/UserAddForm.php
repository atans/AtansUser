<?php
namespace AtansUser\Form;

use AtansLogger\Options\ModuleOptions;
use AtansUser\Entity\User;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineModule\Validator\UniqueObject;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class UserAddForm extends ProvidesEventsForm implements InputFilterProviderInterface
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
        'User' => 'AtansUser\Entity\User',
    );

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * Initialization
     *
     * @param ServiceManager $serviceManager
     * @param string $name
     */
    public function __construct(ServiceManager $serviceManager, $name = 'user-add-form')
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->setServiceManager($serviceManager);

        $objectManager = $this->getObjectManager();
        $this->setHydrator(new DoctrineObject($objectManager))
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

        $userRoles = new ObjectMultiCheckbox('roles');
        $userRoles->setLabel('Roles')
                  ->setLabelAttributes(array(
                      'class' => 'checkbox-inline'
                  ))
                  ->setOptions(array(
                      'use_hidden_element' => true,
                      'object_manager' => $objectManager,
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
                   'value_options' => $serviceManager->get('atansuser_user_statuses'),
               ));
        $this->add($status);

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
            'username' => array(
                'requred' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'object_manager'    => $objectManager,
                            'object_repository' => $objectManager->getRepository($this->entities['User']),
                            'fields' => array('username'),
                            'messages' => array(
                                UniqueObject::ERROR_OBJECT_NOT_UNIQUE => 'The username already taken',
                            ),
                        ),
                    ),
                ),
            ),
            'email' => array(
                'required' => true,
                'validators' => array(
                    array('name' => 'EmailAddress'),
                    array(
                        'name' => 'DoctrineModule\Validator\UniqueObject',
                        'options' => array(
                            'object_manager'    => $objectManager,
                            'object_repository' => $objectManager->getRepository($this->entities['User']),
                            'fields' => array('email'),
                            'messages' => array(
                                UniqueObject::ERROR_OBJECT_NOT_UNIQUE => 'The email already taken',
                            ),
                        ),
                    ),
                ),
            ),
            'password' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            'roles' => array(
                'required' => false,
            ),
            'status' => array(
                'required' => true,
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
     * @return UserAddForm
     */
    public function setModuleOptions(ModuleOptions $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
        return $this;
    }

    /**
     * Get objectManager
     *
     * @return EntityManager
     */
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManager) {
            $this->setObjectManager($this->getServiceManager()->get($this->getModuleOptions()->getObjectManagerName()));
        }
        return $this->objectManager;
    }

    /**
     * Set objectManager
     *
     * @param  EntityManager $objectManager
     * @return UserAddForm
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
     * @return UserAddForm
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
