<?php
namespace AtansUser\Controller;

use AtansUser\Entity\Permission;
use AtansUser\Module;
use AtansUser\Options\ModuleOptions;
use AtansUser\Service\PermissionAdmin as PermissionAdminService;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class PermissionAdminController extends AbstractActionController
{
    const FLASHMESSENGER_NAMESPACE = 'atansuser-permission-admin-index';

    /**
     * @var array
     */
    protected $entities = array(
        'Permission' => 'AtansUser\Entity\Permission',
    );

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Form
     */
    protected $permissionForm;

    /**
     * @var PermissionAdminService
     */
    protected $permissionAdminService;

    /**
     * @var Form
     */
    protected $permissionSearchForm;

    public function indexAction()
    {
        $request        = $this->getRequest();
        $objectManger   = $this->objectManager($this->getOptions()->getObjectManagerName());

        $data = array(
            'page'   => $request->getQuery('page', 1),
            'size'   => $request->getQuery('size', 10),
            'query'  => $request->getQuery('query', ''),
            'order'  => $request->getQuery('order', 'DESC'),
        );

        $form = $this->getPermissionSearchForm();
        $form->setData($data);
        $form->isValid();

        $paginator = $objectManger->getRepository($this->entities['Permission'])->pagination($form->getData());

        return array(
            'form'      => $form,
            'paginator' => $paginator,
        );
    }

    public function addAction()
    {
        $translator     = $this->getServiceLocator()->get('Translator');
        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);

        $permission = new Permission();

        $form = $this->getPermissionForm();
        $form->bind($permission);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getPermissionAdminService()->add($permission);

                $flashMessenger->addSuccessMessage(sprintf(
                    $translator->translate("Permission '%s' was successfully created", Module::TRANSLATOR_TEXT_DOMAIN),
                    $permission->getName()
                ));

                return $this->redirect()->toRoute('zfcadmin/user/permission');
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
        $objectManager  = $this->objectManager($this->getOptions()->getObjectManagerName());

        $permissionRepository = $objectManager->getRepository($this->entities['Permission']);
        $permission           = $permissionRepository->find($id);
        if (! $permission) {
            $flashMessenger->addErrorMessage(sprintf(
                $translator->translate("Permission does not found. '#%d'"),
                $id
            ));

            return $this->redirect()->toRoute('zfcadmin/user/permission');
        }

        $form = $this->getPermissionForm();
        $form->bind($permission);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getPermissionAdminService()->edit($permission);

                $flashMessenger->addSuccessMessage(sprintf(
                    $translator->translate("Permission '%s' was successfully updated", Module::TRANSLATOR_TEXT_DOMAIN),
                    $permission->getName()
                ));

                return $this->redirect()->toRoute('zfcadmin/user/permission');
            }
        }

        return array(
            'form'       => $form,
            'permission' => $permission,
        );
    }

    public function deleteAction()
    {
        $flashMessenger = $this->flashMessenger()->setNamespace(static::FLASHMESSENGER_NAMESPACE);
        $id             = (int) $this->params()->fromRoute('id', 0);
        $translator     = $this->getServiceLocator()->get('Translator');
        $objectManager  = $this->objectManager($this->getOptions()->getObjectManagerName());

        $permission = $objectManager->find($this->entities['Permission'], $id);
        if (! $permission) {
            $flashMessenger->addErrorMessage(sprintf(
                $translator->translate("Permission does not found. '#%d'", Module::TRANSLATOR_TEXT_DOMAIN),
                $id
            ));

            return $this->redirect()->toRoute('zfcadmin/user/permission');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $delete = $request->getPost('delete');
            if ($delete == 'Yes') {
                $this->getPermissionAdminService()->delete($permission);

                $flashMessenger->addSuccessMessage(sprintf(
                     $translator->translate("Permission '%s' was successfully deleted", Module::TRANSLATOR_TEXT_DOMAIN),
                     $permission->getName()
                 ));

                return $this->redirect()->toRoute('zfcadmin/user/permission');
            }
        }

        return array(
            'permission' => $permission,
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
     * @return PermissionAdminController
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get permissionAdminService
     *
     * @return PermissionAdminService
     */
    public function getPermissionAdminService()
    {
        if (! $this->permissionAdminService instanceof PermissionAdminService) {
            $this->setPermissionAdminService($this->getServiceLocator()->get('atansuser_permission_admin_service'));
        }
        return $this->permissionAdminService;
    }

    /**
     * Set permissionAdminService
     *
     * @param  PermissionAdminService$permissionAdminService
     * @return PermissionAdminController
     */
    public function setPermissionAdminService(PermissionAdminService $permissionAdminService)
    {
        $this->permissionAdminService = $permissionAdminService;
        return $this;
    }

    /**
     * Get permissionForm
     *
     * @return Form
     */
    public function getPermissionForm()
    {
        if (! $this->permissionForm instanceof Form) {
            $this->setPermissionForm($this->getServiceLocator()->get('atansuser_permission_form'));
        }
        return $this->permissionForm;
    }

    /**
     * Set permissionForm
     *
     * @param  Form $permissionForm
     * @return PermissionAdminController
     */
    public function setPermissionForm(Form $permissionForm)
    {
        $this->permissionForm = $permissionForm;
        return $this;
    }

    /**
     * Get permissionSearchForm
     *
     * @return Form
     */
    public function getPermissionSearchForm()
    {
        if (! $this->permissionSearchForm instanceof Form) {
            $this->setPermissionSearchForm($this->getServiceLocator()->get('atansuser_permission_search_form'));
        }
        return $this->permissionSearchForm;
    }

    /**
     * Set permissionSearchForm
     *
     * @param  Form $permissionSearchForm
     * @return PermissionAdminController
     */
    public function setPermissionSearchForm($permissionSearchForm)
    {
        $this->permissionSearchForm = $permissionSearchForm;
        return $this;
    }
}
