<?php
namespace AtansUser\Controller;

use AtansUser\Entity\Role;
use AtansUser\Module;
use AtansUser\Options\ModuleOptions;
use AtansUser\Service\RoleAdmin as RoleAdminService;
use Doctrine\ORM\EntityManagerInterface;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use ZfcRbac\Exception\UnauthorizedException;

class RoleAdminController extends AbstractActionController
{
    /**
     * Flash messenger namespace
     *
     * @var string
     */
    const FLASHMESSENGER_NAMESPACE = 'atansuser-admin-role-index';

    /**
     * @var array
     */
    protected $entities = array(
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
    protected $roleForm;

    /**
     * @var RoleAdminService
     */
    protected $roleAdminService;

    /**
     * @var Form
     */
    protected $roleSearchForm;

    public function indexAction()
    {
        if (! $this->isGranted('atansuser.admin.role.index')) {
            throw new UnauthorizedException();
        }

        $request       = $this->getRequest();
        $objectManager = $this->getObjectManager();

        $data = array(
            'page'   => $request->getQuery('page', 1),
            'count'  => $request->getQuery('count', 10),
            'query'  => $request->getQuery('query', ''),
            'order'  => $request->getQuery('order', 'DESC'),
        );

        $form = $this->getRoleSearchForm();
        $form->setData($data);
        $form->isValid();

        $paginator = $objectManager->getRepository($this->entities['Role'])->pagination($form->getData());

        return array(
            'form'      => $form,
            'paginator' => $paginator,
        );
    }

    public function addAction()
    {
        if (! $this->isGranted('atansuser.admin.role.add')) {
            throw new UnauthorizedException();
        }

        $translator     = $this->getServiceLocator()->get('Translator');
        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);

        $role = new Role();

        $form = $this->getRoleForm();
        $form->bind($role);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getRoleAdminService()->add($role);

                $flashMessenger->addSuccessMessage(sprintf(
                    $translator->translate("Role '%s' was successfully created", Module::TRANSLATOR_TEXT_DOMAIN),
                    $role->getName()
                ));

                return $this->redirect()->toRoute('zfcadmin/user/role');
            }
        }

        return array(
            'form' => $form,
        );
    }

    public function editAction()
    {
        if (! $this->isGranted('atansuser.admin.role.edit')) {
            throw new UnauthorizedException();
        }

        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);
        $id             = (int) $this->params()->fromRoute('id', 0);
        $translator     = $this->getServiceLocator()->get('Translator');
        $objectManager  = $this->getObjectManager();

        $role = $objectManager->find($this->entities['Role'], $id);
        if (! $role) {
            $flashMessenger->addSuccessMessage(sprintf(
                $translator->translate("Role does not found. '#%d'", Module::TRANSLATOR_TEXT_DOMAIN),
                $id
            ));

            return $this->redirect()->toRoute('zfcadmin/user/role');
        }

        $form = $this->getRoleForm();
        $form->bind($role);

        // Ignore self
        $form->get('children')->getProxy()->setIsMethod(true)->setFindMethod(array(
            'name'   => 'findAllWithoutId',
            'params' => array('id' => $id),
        ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getRoleAdminService()->edit($role);

                $flashMessenger->addSuccessMessage(sprintf(
                     $translator->translate("Role '%s' was successfully updated", Module::TRANSLATOR_TEXT_DOMAIN),
                     $role->getName()
                 ));

                return $this->redirect()->toRoute('zfcadmin/user/role');
            }
        }

        return array(
            'form' => $form,
            'role' => $role,
        );
    }

    public function deleteAction()
    {
        if (! $this->isGranted('atansuser.admin.role.delete')) {
            throw new UnauthorizedException();
        }

        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);
        $id            = (int) $this->params()->fromRoute('id', 0);
        $translator    = $this->getServiceLocator()->get('Translator');
        $objectManager = $this->getObjectManager();

        $role = $objectManager->find($this->entities['Role'], $id);
        if (! $role) {
            $flashMessenger->addMessage(sprintf(
                $translator->translate("Role does not found. '#%d'", Module::TRANSLATOR_TEXT_DOMAIN),
                $id
            ));

            return $this->redirect()->toRoute('zfcadmin/user/role');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $delete = $request->getPost('delete');
            if ($delete == 'Yes') {
                $this->getRoleAdminService()->delete($role);

                $flashMessenger->addSuccessMessage(sprintf(
                    $translator->translate("Role '%s' was successfully deleted", Module::TRANSLATOR_TEXT_DOMAIN),
                    $role->getName()
                ));

                return $this->redirect()->toRoute('zfcadmin/user/role');
            }
        }

        return array(
            'role' => $role,
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
     * @param  ModuleOptions $options
     * @return RoleAdminController
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
     * @return RoleAdminController
     */
    public function setObjectManager(EntityManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        return $this;
    }

    /**
     * Get roleForm
     *
     * @return Form
     */
    public function getRoleForm()
    {
        if (! $this->roleForm instanceof Form) {
            $this->setRoleForm($this->getServiceLocator()->get('atansuser_role_form'));
        }
        return $this->roleForm;
    }

    /**
     * Set roleForm
     *
     * @param  Form $roleForm
     * @return RoleAdminController
     */
    public function setRoleForm(Form $roleForm)
    {
        $this->roleForm = $roleForm;
        return $this;
    }

    /**
     * Get roleAdminService
     *
     * @return RoleAdminService
     */
    public function getRoleAdminService()
    {
        if (! $this->roleAdminService instanceof RoleAdminService) {
            $this->setRoleAdminService($this->getServiceLocator()->get('atansuser_role_admin_service'));
        }
        return $this->roleAdminService;
    }

    /**
     * Set roleAdminService
     *
     * @param  RoleAdminService $roleAdminService
     * @return RoleAdminController
     */
    public function setRoleAdminService(RoleAdminService $roleAdminService)
    {
        $this->roleAdminService = $roleAdminService;
        return $this;
    }

    /**
     * Get roleSearchForm
     *
     * @return Form
     */
    public function getRoleSearchForm()
    {
        if (! $this->roleSearchForm instanceof Form) {
            $this->setRoleSearchForm($this->getServiceLocator()->get('atansuser_role_search_form'));
        }
        return $this->roleSearchForm;
    }

    /**
     * Set roleSearchForm
     *
     * @param  Form $roleSearchForm
     * @return RoleAdminController
     */
    public function setRoleSearchForm(Form $roleSearchForm)
    {
        $this->roleSearchForm = $roleSearchForm;
        return $this;
    }
}
