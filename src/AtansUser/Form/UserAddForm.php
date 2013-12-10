<?php
namespace AtansUser\Form;

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
     * @var EntityManager
     */
    protected $entityManager;

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

    public function __construct(ServiceManager $serviceManager, $name = 'user-add-form')
    {
        parent::__construct($name);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

        $this->setServiceManager($serviceManager);

        $entityManager = $this->getEntityManager();
        $this->setHydrator(new DoctrineObject($entityManager))
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
                      'object_manager' => $entityManager,
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
        $entityManager = $this->getEntityManager();

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
                            'object_manager'    => $entityManager,
                            'object_repository' => $entityManager->getRepository($this->entities['User']),
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
                            'object_manager'    => $entityManager,
                            'object_repository' => $entityManager->getRepository($this->entities['User']),
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
     * Get entityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (! $this->entityManager instanceof EntityManager) {
            $this->setEntityManager($this->getServiceManager()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return UserAddForm
     */
    public function setEntityManager($entityManager)
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
     * @return UserAddForm
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}
