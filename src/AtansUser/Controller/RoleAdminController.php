<?php
namespace AtansUser\Controller;

use AtansUser\Entity\Role;
use AtansUser\Options\ModuleOptions;
use AtansUser\Service\RoleAdmin as RoleAdminService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class RoleAdminController extends AbstractActionController
{
    /**
     * Flash messenger namespace
     *
     * @var string
     */
    const FLASHMESSENGER_NAMESPACE = 'atansuser-role-admin-index';

    /**
     * Translator text domain
     */
    const TRANSLATOR_TEXT_DOMAIN = 'AtansUser';

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
        $request       = $this->getRequest();
        $objectManager = $this->objectManager($this->getOptions()->getObjectManager());

        $data = array(
            'page'   => $request->getQuery('page', 1),
            'size'   => $request->getQuery('size', 10),
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
                    $translator->translate("Role '%s' was successfully created", static::TRANSLATOR_TEXT_DOMAIN),
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
        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);
        $id             = (int) $this->params()->fromRoute('id', 0);
        $translator     = $this->getServiceLocator()->get('Translator');
        $objectManager  = $this->objectManager($this->getOptions()->getObjectManager());

        $role = $objectManager->find($this->entities['Role'], $id);
        if (! $role) {
            $flashMessenger->addSuccessMessage(sprintf(
                $translator->translate("Role does not found. '#%d'", static::TRANSLATOR_TEXT_DOMAIN),
                $id
            ));

            return $this->redirect()->toRoute('zfcadmin/user/role');
        }

        $form = $this->getRoleForm();
        $form->bind($role);

        $parentProxy = $form->get('parent')->getProxy();
        $parentProxy->setIsMethod(true)
                    ->setFindMethod(array(
                        'name'   => 'findAllRoleWithoutId',
                        'params' => array('id' => $id),
                    ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getRoleAdminService()->edit($role);

                $flashMessenger->addSuccessMessage(sprintf(
                     $translator->translate("Role '%s' was successfully updated", static::TRANSLATOR_TEXT_DOMAIN),
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
        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);
        $id            = (int) $this->params()->fromRoute('id', 0);
        $translator    = $this->getServiceLocator()->get('Translator');
        $objectManager = $this->objectManager($this->getOptions()->getObjectManager());

        $role = $objectManager->find($this->entities['Role'], $id);
        if (! $role) {
            $flashMessenger->addMessage(sprintf(
                $translator->translate("Role does not found. '#%d'", static::TRANSLATOR_TEXT_DOMAIN),
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
                    $translator->translate("Role '%s' was successfully deleted", static::TRANSLATOR_TEXT_DOMAIN),
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
