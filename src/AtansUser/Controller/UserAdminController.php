<?php
namespace AtansUser\Controller;

use AtansUser\Entity\User;
use AtansUser\Options\ModuleOptions;
use AtansUser\Service\UserAdmin as UserAdminService;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Mvc\Controller\AbstractActionController;

class UserAdminController extends AbstractActionController
{
    /**
     * Flash messenger name space
     *
     * @var string
     */
    const FM_NS = 'atansuser-user-admin-index';

    /**
     * Translator text domain
     */
    const TRANSLATOR_TEXT_DOMAIN = 'AtansUser';

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
        'Role' => 'AtansUser\Entity\Role',
    );

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Form
     */
    protected $userAddForm;

    /**
     * @var Form
     */
    protected $userEditForm;

    /**
     * @var UserAdminService
     */
    protected $userAdminService;

    /**
     * @var Form
     */
    protected $userSearchForm;

    public function indexAction()
    {
        $request        = $this->getRequest();
        $userRepository = $this->getEntityManager()->getRepository($this->entities['User']);

        $data = array(
            'page'   => $request->getQuery('page', 1),
            'size'   => $request->getQuery('size', 10),
            'query'  => $request->getQuery('query', ''),
            'status' => $request->getQuery('status', ''),
            'order'  => $request->getQuery('order', 'DESC'),
        );

        $form = $this->getUserSearchForm();
        $form->setData($data);
        $form->isValid();

        $paginator = $userRepository->pagination($form->getData());

        return array(
            'form'      => $form,
            'paginator' => $paginator,
        );
    }

    public function addAction()
    {
        $form           = $this->getUserAddForm();
        $request        = $this->getRequest();
        $translator     = $this->getServiceLocator()->get('Translator');
        $flashMessenger = $this->flashMessenger()->setNamespace(self::FM_NS);

        $user = new User();
        $user->setStatus($this->getOptions()->getUserDefaultStatus());
        $form->bind($user);
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getUserAdminService()->add($user);

                $flashMessenger->addSuccessMessage(sprintf(
                    $translator->translate("User '%s' was successfully created.", self::TRANSLATOR_TEXT_DOMAIN),
                    $user->getUsername()
                ));

                return $this->redirect()->toRoute('zfcadmin/user');
            }
        }

        return array(
            'form' => $form,
        );
    }

    public function editAction()
    {
        $entityManager  = $this->getEntityManager();
        $id             = (int)$this->params()->fromRoute('id');
        $translator     = $this->getServiceLocator()->get('Translator');
        $request        = $this->getRequest();
        $flashMessenger = $this->flashMessenger()->setNamespace(self::FM_NS);

        $user = $entityManager->find($this->entities['User'], $id);
        if (!$user) {
            $flashMessenger->addMessage(sprintf(
                $translator->translate("User does not found. '#%d'", self::TRANSLATOR_TEXT_DOMAIN),
                $id
            ));

            return $this->redirect()->toRoute('zfcadmin/user');
        }

        $form = $this->getUserEditForm();
        $form->bind($user);
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData(FormInterface::VALUES_AS_ARRAY);
                $this->getUserAdminService()->edit($user, $data['newPassword']);

                $flashMessenger->addSuccessMessage(sprintf(
                     $translator->translate("User '%s' was successfully updated.", self::TRANSLATOR_TEXT_DOMAIN),
                     $user->getUsername()
                 ));

                return $this->redirect()->toRoute('zfcadmin/user');
            }
        }

        return array(
            'form' => $form,
            'user' => $user,
        );
    }

    public function deleteAction()
    {
        $entityManager  = $this->getEntityManager();
        $id             = (int)$this->params()->fromRoute('id');
        $translator     = $this->getServiceLocator()->get('Translator');
        $request        = $this->getRequest();
        $flashMessenger = $this->flashMessenger()->setNamespace(self::FM_NS);

        $user = $entityManager->find($this->entities['User'], $id);
        if (!$user) {
            $this->flashMessenger()
                ->setNamespace(self::FM_NS)
                ->addMessage(sprintf($translator->translate("User does not found. '#%d'", self::TRANSLATOR_TEXT_DOMAIN), $id));

            return $this->redirect()->toRoute('zfcadmin/user');
        }

        if ($request->isPost()) {
            $delete = $request->getPost('delete', 'No');
            if ($delete == 'Yes') {
                $this->getUserAdminService()->delete($user);

                $flashMessenger->addSuccessMessage(sprintf(
                     $translator->translate("User '%s' was successfully deleted.", self::TRANSLATOR_TEXT_DOMAIN),
                     $user->getUsername()
                 ));

                return $this->redirect()->toRoute('zfcadmin/user');
            }
        }

        return array(
            'user' => $user,
        );
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
     * Get entityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (! $this->entityManager instanceof EntityManager) {
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
     * Get userAddForm
     *
     * @return Form
     */
    public function getUserAddForm()
    {
        if (! $this->userAddForm instanceof Form) {
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

    /**
     * Get userEditForm
     *
     * @return Form
     */
    public function getUserEditForm()
    {
        if (! $this->userEditForm instanceof Form) {
            $this->setUserEditForm($this->getServiceLocator()->get('atansuser_user_edit_form'));
        }
        return $this->userEditForm;
    }

    /**
     * Set userEditForm
     *
     * @param  Form $userEditForm
     * @return UserController
     */
    public function setUserEditForm($userEditForm)
    {
        $this->userEditForm = $userEditForm;
        return $this;
    }

    /**
     * Get userAdminService
     *
     * @return UserAdminService
     */
    public function getUserAdminService()
    {
        if (! $this->userAdminService instanceof UserAdminService) {
            $this->setUserAdminService($this->getServiceLocator()->get('atansuser_user_admin_service'));
        }
        return $this->userAdminService;
    }

    /**
     * Set userAdminService
     *
     * @param  UserAdminService $userAdminService
     * @return UserAdminController
     */
    public function setUserAdminService(UserAdminService $userAdminService)
    {
        $this->userAdminService = $userAdminService;
        return $this;
    }

    /**
     * Get userSearchForm
     *
     * @return Form
     */
    public function getUserSearchForm()
    {
        if (! $this->userSearchForm instanceof Form) {
            $this->setUserSearchForm($this->getServiceLocator()->get('atansuser_user_search_form'));
        }
        return $this->userSearchForm;
    }

    /**
     * Set userSearchForm
     *
     * @param  Form $userSearchForm
     * @return UserAdminController
     */
    public function setUserSearchForm($userSearchForm)
    {
        $this->userSearchForm = $userSearchForm;
        return $this;
    }
}
