<?php
namespace AtansUser\Controller;

use AtansUser\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{
    const FLASH_MESSENGER_NAME_SPACE = 'atansuser-user-index';

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
        'User' => 'User\Entity\User',
        'Role' => 'User\Entity\Role',
    );

    /**
     * @var Form
     */
    protected $userAddForm;

    public function indexAction()
    {
        $userRepository = $this->getEntityManager()->getRepository($this->entities['user']);
        $returns = array(
            'users'         => $userRepository->findAll(),
            'flashMessages' => null,
        );

        return $returns;
    }

    public function addAction()
    {
        $entityManager = $this->getEntityManager();
        $form          = $this->getUserAddForm();
        $request       = $this->getRequest();
        $translator    = $this->getServiceLocator()->get('Translator');

        $user = new User();
        $form->bind($user);
        if ($request->isPost()) {
            $form->setData($request->getData());
            if ($form->isValid()) {
                $datTime = new DateTime();
                $user->setCreated($datTime);
                $user->setModified($datTime);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->flashMessenger()
                     ->setNamespace(self::FLASH_MESSENGER_NAME_SPACE)
                     ->addMessage(sprintf(
                        $translator->translate("新增用戶成功"),
                        $user->getUsername()
                    ));

                return $this->redirect()->toRoute('atansuser/user');
            }
        }

        return array(
            'form' => $form,
        );
    }


    public function loginAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            // TODO 用户已停用

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
     * Get userAddForm
     *
     * @return Form
     */
    public function getUserAddForm()
    {
        if (!$this->userAddForm instanceof Form) {
            $this->setUserAddForm($this->getServiceLocator()->get('atansuser_user_add_form'));
        }
        return $this->userAddForm;
    }

    /**
     * Set userAddForm
     *
     * @param  Form $userAddForm
     * @return UserController
     */
    public function setUserAddForm(Form $userAddForm)
    {
        $this->userAddForm = $userAddForm;
        return $this;
    }
}
