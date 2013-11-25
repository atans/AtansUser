<?php
namespace AtansUser\Controller;

use AtansUser\Entity\User;
use AtansUser\Options\ModuleOptions;
use AtansUser\Service\User as UserService;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Stdlib\Parameters;
use Zend\Validator\EmailAddress;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    const ROUTE_LOGIN           = 'atansuser/login';
    const ROUTE_LOGOUT          = 'atansuser/logout';
    const ROUTE_REGISTER        = 'atansuser/register';
    const ROUTE_CHANGE_PASSWORD = 'atansuser/change-password';

    const CONTROLLER_NAME = 'AtansUser\Controller\User';

    /**
     * Flash messenger name space
     *
     * @var string
     */
    const FM_NS = 'atansuser-user-index';

    /**
     * Translator text domain
     */
    const TRANSLATOR_TEXT_DOMAIN = 'AtansUser';

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var Form
     */
    protected $changePasswordForm;

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
     * @var Form
     */
    protected $registerForm;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var UserService
     */
    protected $userService;

    public function indexAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate($this->getOptions()->getUserIndexTemplate());

        return $viewModel;
    }

    public function loginAction()
    {
        $form    = $this->getLoginForm();
        $request = $this->getRequest();

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery('redirect')) {
            $redirect = $request->getQuery('redirect');
        } else {
            $redirect = false;
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $request->setPost(new Parameters($form->getData()));
                return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
            }
        }

        return array(
            'form'     => $form,
            'redirect' => $redirect,
        );
    }

    public function registerAction()
    {
        if ($this->getAuthenticationService()->getIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        if (!$this->getOptions()->getEnableRegistration()) {
            return array(
                'enableRegistration' => false,
            );
        }

        $request    = $this->getRequest();
        $form       = $this->getRegisterForm();
        $translator = $this->getServiceLocator()->get('Translator');

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $request->getQuery()->get('redirect')) {
            $redirect = $request->getQuery()->get('redirect');
        } else {
            $redirect = false;
        }

        $redirectUrl = $this->url()->fromRoute(static::ROUTE_REGISTER) . ($redirect ? '?redirect=' . rawurlencode($redirect) : '');

        $prg = $this->prg($redirectUrl, true);

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'form'               => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect'           => $redirect,
            );
        }

        $user = $this->getUserService()->register($prg);

        if (!$user) {
            return array(
                'form'               => $form,
                'enableRegistration' => $this->getOptions()->getEnableRegistration(),
                'redirect'           => $redirect,
            );
        }

        if ($this->getOptions()->getLoginAfterRegistration()) {
            $identityFields = $this->getOptions()->getAuthIdentityFields();
            $post = array();
            if (in_array('email', $identityFields)) {
                $post['identity'] = $user->getEmail();
            } elseif (in_array('username', $identityFields)) {
                $post['identity'] = $user->getUsername();
            }
            $post['credential'] = $prg['password'];
            $request->setPost(new Parameters($post));

            return $this->forward()->dispatch(static::CONTROLLER_NAME, array('action' => 'authenticate'));
        }

        $this->flashMessenger()
             ->setNamespace('atansuser-user-login')
             ->addSuccessMessage($translator->translate('Your account has been successfully registered.'));


        return $this->redirect()->toUrl(
            $this->url()->fromRoute(static::ROUTE_LOGIN) . ($redirect ? '?redirect=' . rawurlencode($redirect) : '')
        );
    }

    public function authenticateAction()
    {
        if ($this->getAuthenticationService()->getIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $request = $this->getRequest();

        $identity   = $request->getPost('identity');
        $credential = $request->getPost('credential');
        $redirect   = $request->getPost('redirect', $request->getQuery('redirect', false));

        $flashMessenger = $this->flashMessenger()->setNamespace('atansuser-user-login');
        $translator     = $this->getServiceLocator()->get('Translator');

        $authService = $this->getAuthenticationService();

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        $adapter = $authService->getAdapter();
        $adapterOptions = $adapter->getOptions();
        $adapterOptions->setCredentialCallable(function(User $user, $passwordGiven) use ($bcrypt) {
            return $bcrypt->verify($passwordGiven, $user->getPassword());
        });

        $identityFields = $this->getOptions()->getAuthIdentityFields();
        $emailValidator = new EmailAddress();
        if (in_array('email', $identityFields) && $emailValidator->isValid($identity)) {
            $adapterOptions->setIdentityProperty('email');
        } else {
            $adapterOptions->setIdentityProperty('username');
        }

        $adapter->setIdentityValue($identity);
        $adapter->setCredentialValue($credential);
        $authResult = $authService->authenticate();

        if (!$authResult->isValid()) {
            switch ($authResult->getCode()) {
                case Result::FAILURE:
                    $flashMessenger->addErrorMessage($translator->translate('Authentication Failure.', static::TRANSLATOR_TEXT_DOMAIN));
                    break;
                case Result::FAILURE_IDENTITY_NOT_FOUND:
                    $flashMessenger->addErrorMessage($translator->translate('A record with the supplied identity could not be found.', static::TRANSLATOR_TEXT_DOMAIN));
                    break;
                case Result::FAILURE_IDENTITY_AMBIGUOUS:
                    $flashMessenger->addErrorMessage($translator->translate('More than one record matches the supplied identity.', static::TRANSLATOR_TEXT_DOMAIN));
                    break;
                case Result::FAILURE_CREDENTIAL_INVALID:
                    $flashMessenger->addErrorMessage($translator->translate('Supplied credential is invalid.', static::TRANSLATOR_TEXT_DOMAIN));
                    break;
                case Result::FAILURE_UNCATEGORIZED:
                    $flashMessenger->addErrorMessage($translator->translate('Authentication uncategorized error.', static::TRANSLATOR_TEXT_DOMAIN));
                    break;
                default:
                    $flashMessenger->addErrorMessage(sprintf(
                        $translator->translate("Unknown authentication code: %d", static::TRANSLATOR_TEXT_DOMAIN),
                        $authResult->getCode()
                    ));
            }

            if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
                return $this->redirect()->toUrl($this->url()->fromRoute($this->getOptions()->getLogoutRedirectRoute())
                    . ($redirect ? '?redirect=' . rawurlencode($redirect): ''));
            }

            return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());
        }

        $user = $authService->getIdentity();

        if ($this->getOptions()->getEnableUserStatus()) {
            if (!in_array($user->getStatus(), $this->getOptions()->getAllowedLoginStatuses())) {
                $authService->clearIdentity();
                $flashMessenger->addErrorMessage($translator->translate('Your account is not active.', static::TRANSLATOR_TEXT_DOMAIN));
                return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());
            }
        }

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
    }

    public function logoutAction()
    {
        $this->getAuthenticationService()->clearIdentity();

        $redirect = $this->params()->fromQuery('redirect', false);

        if ($this->getOptions()->getUseRedirectParameterIfPresent() && $redirect) {
            return $this->redirect()->toUrl($redirect);
        }

        return $this->redirect()->toRoute($this->getOptions()->getLogoutRedirectRoute());
    }

    public function changePasswordAction()
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $prg = $this->prg(static::ROUTE_CHANGE_PASSWORD);
        $form = $this->getChangePasswordForm();

        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'form'  => $form,
            );
        }

        $form->setData($prg);

        if (!$form->isValid()) {
            return array(
                'form'  => $form,
            );
        }

        $translator     = $this->getServiceLocator()->get('Translator');
        $flashMessenger = $this->flashMessenger()->setNamespace('atansuser-user-change-password');

        if (!$this->getUserService()->changePassword($form->getData())) {
            $flashMessenger->addMessage($translator->translate('Your current password was incorrectly typed.', static::TRANSLATOR_TEXT_DOMAIN));

            return $this->redirect()->toRoute(static::ROUTE_CHANGE_PASSWORD);
        }

        $flashMessenger->addSuccessMessage($translator->translate('Password changed successfully.', static::TRANSLATOR_TEXT_DOMAIN));

        return $this->redirect()->toRoute(static::ROUTE_CHANGE_PASSWORD);
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
     * Get changePasswordForm
     *
     * @return Form
     */
    public function getChangePasswordForm()
    {
        if (!$this->changePasswordForm instanceof Form) {
            $this->setChangePasswordForm($this->getServiceLocator()->get('atansuser_change_password_form'));
        }
        return $this->changePasswordForm;
    }

    /**
     * Set changePasswordForm
     *
     * @param  Form $changePasswordForm
     * @return UserController
     */
    public function setChangePasswordForm($changePasswordForm)
    {
        $this->changePasswordForm = $changePasswordForm;

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

    /**
     * Get registerForm
     *
     * @return Form
     */
    public function getRegisterForm()
    {
        if (!$this->registerForm instanceof Form) {
            $this->setRegisterForm($this->getServiceLocator()->get('atansuser_register_form'));
        }
        return $this->registerForm;
    }

    /**
     * Set registerForm
     *
     * @param  Form $registerForm
     * @return UserController
     */
    public function setRegisterForm(Form $registerForm)
    {
        $this->registerForm = $registerForm;
        return $this;
    }

    /**
     * Get userService
     *
     * @return UserService
     */
    public function getUserService()
    {
        if (!$this->userService instanceof UserService) {
            $this->setUserService($this->getServiceLocator()->get('atansuser_user_service'));
        }
        return $this->userService;
    }

    /**
     * Set userService
     *
     * @param  UserService $userService
     * @return UserController
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }
}
