<?php
namespace AtansUser\Controller;

use AtansUser\Module;
use AtansUser\Options\ModuleOptions;
use AtansUser\Service\UserAdmin as UserAdminService;
use Doctrine\ORM\EntityManagerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Form\Form;
use Zend\Form\FormInterface;
use Zend\Mvc\Controller\AbstractActionController;
use ZfcRbac\Exception\UnauthorizedException;

class UserAdminController extends AbstractActionController
{
    /**
     * Flash messenger name space
     *
     * @var string
     */
    const FLASHMESSENGER_NAMESPACE = 'atansuser-admin-user-index';

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
     * @var EntityManagerInterface
     */
    protected $objectManager;

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

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->entities = $config['entities'];
    }
    
    public function indexAction()
    {
        if (! $this->isGranted('atansuser.admin.user.index')) {
            throw new UnauthorizedException();
        }

        $request       = $this->getRequest();
        $objectManager = $this->getObjectManager();

        $data = array(
            'page'   => $request->getQuery('page', 1),
            'count'  => $request->getQuery('count', $this->getOptions()->getUserAdminCountPerPage()),
            'query'  => $request->getQuery('query', ''),
            'status' => $request->getQuery('status', ''),
            'order'  => $request->getQuery('order', 'DESC'),
        );

        $form = $this->getUserSearchForm();
        $form->setData($data);
        $form->isValid();

        $paginator = $objectManager->getRepository($this->entities['User'])->pagination($form->getData());

        return array(
            'form'      => $form,
            'paginator' => $paginator,
            'statuses'  => $this->getServiceLocator()->get('atansuser_user_status_value_options'),
        );
    }

    public function addAction()
    {
        if (! $this->isGranted('atansuser.admin.user.add')) {
            throw new UnauthorizedException();
        }

        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);
        $request        = $this->getRequest();
        $translator     = $this->getServiceLocator()->get('Translator');

        $user = new $this->entities['User'];
        $user->setStatus($this->getOptions()->getUserDefaultStatus());

        $form = $this->getUserAddForm();
        $form->bind($user);

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getUserAdminService()->add($user);

                $flashMessenger->addSuccessMessage(sprintf(
                    $translator->translate("User '%s' was successfully created", Module::TRANSLATOR_TEXT_DOMAIN),
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
        if (! $this->isGranted('atansuser.admin.user.edit')) {
            throw new UnauthorizedException();
        }

        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);
        $id             = (int)$this->params()->fromRoute('id');
        $translator     = $this->getServiceLocator()->get('Translator');
        $objectManager  = $this->getObjectManager();
        $request        = $this->getRequest();

        $user = $objectManager->find($this->entities['User'], $id);
        if (!$user) {
            $flashMessenger->addMessage(sprintf(
                $translator->translate("User does not found '#%d'", Module::TRANSLATOR_TEXT_DOMAIN),
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
                     $translator->translate("User '%s' was successfully updated", Module::TRANSLATOR_TEXT_DOMAIN),
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
        if (! $this->isGranted('atansuser.admin.user.delete')) {
            throw new UnauthorizedException();
        }

        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);
        $id             = (int)$this->params()->fromRoute('id');
        $objectManager  = $this->getObjectManager();
        $request        = $this->getRequest();
        $translator     = $this->getServiceLocator()->get('Translator');

        $user = $objectManager->find($this->entities['User'], $id);
        if (!$user) {
            $this->flashMessenger()
                ->setNamespace(static::FLASHMESSENGER_NAMESPACE)
                ->addMessage(sprintf($translator->translate("User does not found '#%d'", Module::TRANSLATOR_TEXT_DOMAIN), $id));

            return $this->redirect()->toRoute('zfcadmin/user');
        }

        if ($request->isPost()) {
            $delete = $request->getPost('delete', 'No');
            if ($delete == 'Yes') {
                $this->getUserAdminService()->delete($user);

                $flashMessenger->addSuccessMessage(sprintf(
                     $translator->translate("User '%s' was successfully deleted", Module::TRANSLATOR_TEXT_DOMAIN),
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
     * Get objectManager
     *
     * @return EntityManagerInterface
     */
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManagerInterface) {
            $this->setObjectManager($this->getServiceLocator()->get($this->getOptions()->getObjectManagerName()));
        }
        return $this->objectManager;
    }

    /**
     * Set objectManager
     *
     * @param  EntityManagerInterface $objectManager
     * @return UserAdminController
     */
    public function setObjectManager(EntityManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
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
    public function setUserEditForm(Form $userEditForm)
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
    public function setUserSearchForm(Form $userSearchForm)
    {
        $this->userSearchForm = $userSearchForm;
        return $this;
    }
}
