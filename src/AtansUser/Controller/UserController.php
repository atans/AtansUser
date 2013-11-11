<?php
namespace AtansUser\Controller;

use AtansUser\Entity\User;
use AtansUser\Options\ModuleOptions;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    const ROUTE_LOGIN  = 'atansuser/login';
    const ROUTE_LOGOUT = 'atansuser/logout';

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
        if (!$this->identity()) {
            return $this->redirect()->toRoute(static::ROUTE_LOGIN);
        }

        $viewModel = new ViewModel(array(
            'flashMessages' => null,
        ));
        if ($flashMessages = $this->flashMessenger()->setNamespace(self::FM_NS)->getMessages()) {
            $viewModel->setVariable('flashMessages', $flashMessages);
        }

        $viewModel->setTemplate($this->getOptions()->getUserIndexTemplate());

        return $viewModel;
    }

    public function loginAction()
    {
        $translator = $this->getServiceLocator()->get('Translator');
        $error = null;
        $form  = $this->getLoginForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $authService = $this->getAuthenticationService();
                $adapter = $authService->getAdapter();
                $adapter->setIdentityValue($data['username']);
                $adapter->setCredentialValue($data['password']);
                $authResult = $authService->authenticate();

                if ($authResult->isValid()) {
                    $user = $authService->getIdentity();
                    if ($user->getStatus() !== User::STATUS_ACTIVE) {
                        $authService->clearIdentity();
                        $error = array(
                            $translator->translate('帳號不能使用'),
                        );
                    } else {
                        return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
                    }
                }
                switch ($authResult->getCode()) {
                    case Result::FAILURE:
                        $error = $translator->translate('失敗');
                        break;
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                        $error = $translator->translate('帳號不存在.');
                        break;
                    case Result::FAILURE_IDENTITY_AMBIGUOUS:
                        $error = $translator->translate('輸入的帳號匹配多個記錄.');
                        break;
                    case Result::FAILURE_CREDENTIAL_INVALID:
                        $error = $translator->translate('密碼不正確.');
                        break;
                    case Result::FAILURE_UNCATEGORIZED:
                        $error = $translator->translate('未知原因失敗.');
                        break;
                    case Result::SUCCESS:
                        $error = $translator->translate('認證成功.');
                        break;
                }

            }
        }

        return array(
            'error' => $error,
            'form'  => $form,
        );
    }

    public function logoutAction()
    {
        $this->getAuthenticationService()->clearIdentity();
        return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());
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
     * Get loginForm
     *
     * @return Form
     */
    public function getLoginForm()
    {
        if (!$this->loginForm instanceof Form) {
            $this->setLoginForm($this->getServiceLocator()->get('atansuser_login_form'));
        }
        return $this->loginForm;
    }

    /**
     * Set loginForm
     *
     * @param  Form $loginForm
     * @return UserController
     */
    public function setLoginForm(Form $loginForm)
    {
        $this->loginForm = $loginForm;
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
