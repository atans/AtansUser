<?php
namespace AtansUser\Form;

use AtansUser\Entity\User;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use DoctrineModule\Validator\NoObjectExists;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\Form\ProvidesEventsForm;

class RegisterForm extends ProvidesEventsForm implements InputFilterProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var array
     */
    protected $entities = array(
        'User' => 'AtansUser\Entity\User',
    );

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct('register-form');

        $this->setServiceManager($serviceManager);

        $this->setHydrator(new DoctrineObject($this->getEntityManager()))
             ->setObject(new User());

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

        $passwordVerify = new Element\Password('passwordVerify');
        $passwordVerify->setLabel('Password verify')
                       ->setAttribute('class', 'form-control');
        $this->add($passwordVerify);

        $next = new Element\Hidden('redirect');
        $this->add($next);

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
        $userRepository = $this->getEntityManager()->getRepository($this->entities['User']);

        return array(
            'username' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'max' => 255,
                        ),
                    ),
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $userRepository,
                            'fields'            => 'username',
                            'messages' => array(
                                NoObjectExists::ERROR_OBJECT_FOUND => 'The username already taken',
                            ),
                        ),
                    ),
                ),
            ),
            'email' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'DoctrineModule\Validator\NoObjectExists',
                        'options' => array(
                            'object_repository' => $userRepository,
                            'fields'            => 'email',
                            'messages' => array(
                                NoObjectExists::ERROR_OBJECT_FOUND => 'The email already taken',
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
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                ),
            ),
            'passwordVerify' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 6,
                        ),
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'password'
                        ),
                    ),
                ),
            ),
            'redirect' => array(
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
        if (!$this->entityManager instanceof EntityManager) {
            $this->setEntityManager($this->getServiceManager()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return RegisterForm
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
     * @return RegisterForm
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}