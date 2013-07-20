<?php
namespace AtansUser\Controller;

use AtansUser\Entity\User;
use AtansUser\Options\ModuleOptions;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{
    /**
     * Flash messenger name space
     *
     * @var string
     */
    const FM_NS = 'atansuser-user-index';

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

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
     * @var Form
     */
    protected $loginForm;

    /**
     * @var From
     */
    protected $registerForm;

    /**
     * @var ModuleOptions
     */
    protected $options;

    public function indexAction()
    {
        $userRepository = $this->getEntityManager()->getRepository($this->entities['User']);
        $returns = array(
            'users'         => $userRepository->findAll(),
            'flashMessages' => null,
        );
        if ($flashMessages = $this->flashMessenger()->setNamespace(self::FM_NS)->getMessages()) {
            $returns['flashMessages'] = $flashMessages;
        }

        return $returns;
    }

    public function loginAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();

            $authService = $this->getAuthenticationService();
            $adapter = $authService->getAdapter();
            $adapter->setIdentityValue($data['username']);
            $adapter->setCredentialValue($data['password']);
            $authResult = $authService->authenticate();

            if ($authResult->isValid()) {
                return $this->redirect()->toRoute('user');
            }
        }

        return array(
            'form' => new \User\Form\LoginForm(),
        );
    }

    /**
     * Get authenticationService
     *
     * @return AuthenticationService
     */
    public function getAuthenticationService()
    {
        if (!$this->authenticationService instanceof AuthenticationService) {
            $this->setAuthenticationService($this->getServiceLocator()->get('Zend\Authentication\AuthenticationService'));
        }
        return $this->authenticationService;
    }

    /**
     * Set authenticationService
     *
     * @param  AuthenticationService $authenticationService
     * @return UserController
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
    public function getEntityManager()
    {
        if (!$this->entityManager instanceof EntityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $entityManager
     * @return UserController
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get options
     *
     * @return ModuleOptions
     */
    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceLocator()->get('atansuser_module_options'));
        }
        return $this->options;
    }

    /**
     * Set options
     *
     * @param ModuleOptions $options
     * @return $this
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }
}
