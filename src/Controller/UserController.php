<?php
namespace AtansUser\Controller;

use AtansUser\Entity\UserInterface;
use AtansUser\Module;
use AtansUser\Options\ModuleOptions;
use AtansUser\Service\User as UserService;
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
    const ROUTE_LOGIN                              = 'atansuser/login';
    const ROUTE_LOGOUT                             = 'atansuser/logout';
    const ROUTE_REGISTER                           = 'atansuser/register';
    const ROUTE_CHANGE_EMAIL                       = 'atansuser/change-email';
    const ROUTE_CHANGE_PASSWORD                    = 'atansuser/change-password';
    const FLASHMESSENGER_LOGIN_NAMESPACE           = 'atansuser-user-login';
    const FLASHMESSENGER_CHANGE_EMAIL_NAMESPACE    = 'atansuser-user-change-email';
    const FLASHMESSENGER_CHANGE_PASSWORD_NAMESPACE = 'atansuser-user-change-password';

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var Form
     */
    protected $changeEmailForm;

    /**
     * @var Form
     */
    protected $changePasswordForm;

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
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Form
     */
    protected $registerForm;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->entities = $config['entities'];
    }

    public function indexAction()
    {
        if (! $this->identity()) {
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
                return $this->forward()->dispatch(UserController::class, array('action' => 'authenticate'));
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

        if (! $this->getOptions()->getEnableRegistration()) {
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

        if (! $user) {
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

            return $this->forward()->dispatch(UserController::class, array('action' => 'authenticate'));
        }

        $this->flashMessenger()
             ->setNamespace(static::FLASHMESSENGER_LOGIN_NAMESPACE)
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

        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_LOGIN_NAMESPACE);
        $translator     = $this->getServiceLocator()->get('Translator');

        $authService = $this->getAuthenticationService();

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());

        $adapter = $authService->getAdapter();
        $adapterOptions = $adapter->getOptions();
        $adapterOptions->setCredentialCallable(function(UserInterface $user, $passwordGiven) use ($bcrypt) {
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

        if (! $authResult->isValid()) {
            switch ($authResult->getCode()) {
                case Result::FAILURE:
                    $flashMessenger->addErrorMessage($translator->translate('Authentication Failure.', Module::TRANSLATOR_TEXT_DOMAIN));
                    break;
                case Result::FAILURE_IDENTITY_NOT_FOUND:
                    $flashMessenger->addErrorMessage($translator->translate('A record with the supplied identity could not be found.', Module::TRANSLATOR_TEXT_DOMAIN));
                    break;
                case Result::FAILURE_IDENTITY_AMBIGUOUS:
                    $flashMessenger->addErrorMessage($translator->translate('More than one record matches the supplied identity.', Module::TRANSLATOR_TEXT_DOMAIN));
                    break;
                case Result::FAILURE_CREDENTIAL_INVALID:
                    $flashMessenger->addErrorMessage($translator->translate('Supplied credential is invalid.', Module::TRANSLATOR_TEXT_DOMAIN));
                    break;
                case Result::FAILURE_UNCATEGORIZED:
                    $flashMessenger->addErrorMessage($translator->translate('Authentication uncategorized error.', Module::TRANSLATOR_TEXT_DOMAIN));
                    break;
                default:
                    $flashMessenger->addErrorMessage(sprintf(
                        $translator->translate("Unknown authentication code: %d", Module::TRANSLATOR_TEXT_DOMAIN),
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
                $flashMessenger->addErrorMessage($translator->translate('Your account is not active.', Module::TRANSLATOR_TEXT_DOMAIN));
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

    public function changeEmailAction()
    {
        if (! $this->getAuthenticationService()->hasIdentity()) {
            return $this->redirect()->toRoute($this->getOptions()->getLoginRedirectRoute());
        }

        $form = $this->getChangeEmailForm();

        $prg = $this->prg(static::ROUTE_CHANGE_EMAIL);
        if ($prg instanceof Response) {
            return $prg;
        } elseif ($prg === false) {
            return array(
                'form' => $form,
            );
        }

        $form->setData($prg);

        if (! $form->isValid()) {
            return array(
                'form' => $form,
            );
        }

        $translator     = $this->getServiceLocator()->get('Translator');
        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_CHANGE_EMAIL_NAMESPACE);

        if (! $this->getUserService()->changeEmail($form->getData())) {
            $flashMessenger->addMessage($translator->translate('Your current password was incorrectly typed.', Module::TRANSLATOR_TEXT_DOMAIN));

            return $this->redirect()->toRoute(static::ROUTE_CHANGE_EMAIL);
        }

        $flashMessenger->addSuccessMessage($translator->translate('Email changed successfully.', Module::TRANSLATOR_TEXT_DOMAIN));

        return $this->redirect()->toRoute(static::ROUTE_CHANGE_EMAIL);
    }

    public function changePasswordAction()
    {
        if (! $this->getAuthenticationService()->hasIdentity()) {
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

        if (! $form->isValid()) {
            return array(
                'form'  => $form,
            );
        }

        $translator     = $this->getServiceLocator()->get('Translator');
        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_CHANGE_PASSWORD_NAMESPACE);

        if (! $this->getUserService()->changePassword($form->getData())) {
            $flashMessenger->addMessage($translator->translate('Your current password was incorrectly typed.', Module::TRANSLATOR_TEXT_DOMAIN));

            return $this->redirect()->toRoute(static::ROUTE_CHANGE_PASSWORD);
        }

        $flashMessenger->addSuccessMessage($translator->translate('Password changed successfully.', Module::TRANSLATOR_TEXT_DOMAIN));

        return $this->redirect()->toRoute(static::ROUTE_CHANGE_PASSWORD);
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
     * @return UserController
     */
    public function setAuthenticationService(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
        return $this;
    }

    /**
     * Get changeEmailForm
     *
     * @return Form
     */
    public function getChangeEmailForm()
    {
        if (! $this->changeEmailForm instanceof Form) {
            $this->setChangeEmailForm($this->getServiceLocator()->get('atansuser_change_email_form'));
        }
        return $this->changeEmailForm;
    }

    /**
     * Set changeEmailForm
     *
     * @param  Form $changeEmailForm
     * @return UserController
     */
    public function setChangeEmailForm($changeEmailForm)
    {
        $this->changeEmailForm = $changeEmailForm;
        return $this;
    }

    /**
     * Get changePasswordForm
     *
     * @return Form
     */
    public function getChangePasswordForm()
    {
        if (! $this->changePasswordForm instanceof Form) {
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
     * Get loginForm
     *
     * @return Form
     */
    public function getLoginForm()
    {
        if (! $this->loginForm instanceof Form) {
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
        if (! $this->options instanceof ModuleOptions) {
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
        if (! $this->registerForm instanceof Form) {
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
        if (! $this->userService instanceof UserService) {
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
