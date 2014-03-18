<?php
namespace AtansUser\Service;

use AtansUser\Options\ModuleOptions;
use DateTime;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\Bcrypt;
use Zend\Form\Form;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\EventManager\EventProvider;

class User extends EventProvider implements ServiceLocatorAwareInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var array
     */
    protected $entities = array(
        'Role' => 'AtansUser\Entity\Role',
    );

    /**
     * @var EntityManager
     */
    protected $objectManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Form
     */
    protected $registerForm;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Change email
     *
     * @param array $data
     * @return bool
     */
    public function changeEmail(array $data)
    {
        $currentUser = $this->getAuthenticationService()->getIdentity();

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (! $bcrypt->verify($data['credential'], $currentUser->getPassword())) {
            return false;
        }

        $currentUser->setEmail($data['newEmail']);

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $currentUser));
        $this->getObjectManager()->persist($currentUser);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $currentUser));

        return true;
    }

    /**
     * Change password
     *
     * @param array $data
     * @return bool
     */
    public function changePassword(array $data)
    {
        $currentUser = $this->getAuthenticationService()->getIdentity();

        $oldPassword = $data['credential'];
        $newPassword = $data['newCredential'];

        $bcrypt  = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        if (! $bcrypt->verify($oldPassword, $currentUser->getPassword())) {
            return false;
        }

        $password = $bcrypt->create($newPassword);
        $currentUser->setPassword($password);

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $currentUser));
        $this->getObjectManager()->persist($currentUser);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $currentUser));

        return true;
    }

    /**
     * Create user from form
     *
     * @param  array $data
     * @return \AtansUser\Entity\User|false
     */
    public function register(array $data)
    {
        $form = $this->getRegisterForm();
        $form->setData($data);

        if (! $form->isValid()) {
            return false;
        }

        $entityManager = $this->getObjectManager();
        $user          = $form->getData();

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());
        $user->setPassword($bcrypt->create($user->getPassword()));

        $user->setStatus($this->getOptions()->getUserDefaultStatus());
        $user->setCreated(new DateTime());

        $defaultsRoles = $this->getOptions()->getUserDefaultRoles();
        if (is_array($defaultsRoles) && count($defaultsRoles) > 0) {
            $roleRepository = $entityManager->getRepository($this->entities['Role']);

            foreach ($defaultsRoles as $roleName) {
                if ($userRole = $roleRepository->findOneBy(array('name' => $roleName))) {
                    $user->addUserRole($userRole);
                }
            }
        }

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $user));
        $this->getObjectManager()->persist($user);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $user));

        return $user;
    }

    /**
     * Get authenticationService
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (! $this->authenticationService instanceof AuthenticationService) {
            $this->setAuthenticationService($this->getServiceLocator()->get('Zend\Authentication\AuthenticationService'));
        }
        return $this->authenticationService;
    }

    /**
     * Set authenticationService
     *
     * @param  AuthenticationService $authenticationService
     * @return User
     */
    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        return $this;
    }

    /**
     * Get entityManager
     *
     * @return EntityManager
     */
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManager) {
            $this->setObjectManager($this->getServiceLocator()->get($this->getOptions()->getObjectManagerName()));
        }
        return $this->objectManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $objectManager
     * @return User
     */
    public function setObjectManager(EntityManager $objectManager)
    {
        $this->objectManager = $objectManager;
        return $this;
    }

    /**
     * Get options
     *
     * @return ModuleOptions
     */
    public function getOptions()
    {
        if (! $this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceLocator()->get('atansuser_module_options'));
        }
        return $this->options;
    }

    /**
     * Set options
     *
     * @param  ModuleOptions $options
     * @return User
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get registerForm
     *
     * @return Form
     */
    public function getRegisterForm()
    {
        if (! $this->registerForm instanceof Form) {
            $this->setRegisterForm($this->getServiceLocator()->get('atansuser_register_form'));
        }
        return $this->registerForm;
    }

    /**
     * Set registerForm
     *
     * @param  Form $registerForm
     * @return User
     */
    public function setRegisterForm($registerForm)
    {
        $this->registerForm = $registerForm;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}
