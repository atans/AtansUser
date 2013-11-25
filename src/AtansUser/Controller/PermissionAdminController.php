<?php
namespace AtansUser\Controller;

use AtansUser\Entity\Permission;
use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class PermissionAdminController extends AbstractActionController
{
    /**
     * Flash messenger name space
     *
     * @var string
     */
    const FM_NS = 'atansuser-permission-admin-index';

    /**
     * Translator text domain
     */
    const TRANSLATOR_TEXT_DOMAIN = 'AtansUser';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $entities = array(
        'Permission' => 'AtansUser\Entity\Permission',
    );

    /**
     * @var Form
     */
    protected $permissionForm;

    /**
     * @var Form
     */
    protected $permissionSearchForm;

    public function indexAction()
    {
        $request        = $this->getRequest();
        $userRepository = $this->getEntityManager()->getRepository($this->entities['Permission']);

        $data = array(
            'page'   => $request->getQuery('page', 1),
            'size'   => $request->getQuery('size', 10),
            'query'  => $request->getQuery('query', ''),
            'order'  => $request->getQuery('order', 'DESC'),
        );

        $form = $this->getPermissionSearchForm();
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
        $entityManager = $this->getEntityManager();
        $translator    = $this->getServiceLocator()->get('Translator');

        $permission = new Permission();

        $form = $this->getPermissionForm();
        $form->bind($permission);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $entityManager->persist($permission);
                $entityManager->flush();

                $this->flashMessenger()
                    ->setNamespace(self::FM_NS)
                    ->addSuccessMessage(sprintf(
                        $translator->translate("Permission '%s' was successfully created.", self::TRANSLATOR_TEXT_DOMAIN),
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
        $entityManager        = $this->getEntityManager();
        $id                   = (int) $this->params()->fromRoute('id', 0);
        $translator           = $this->getServiceLocator()->get('Translator');
        $permissionRepository = $entityManager->getRepository($this->entities['Permission']);

        $permission = $permissionRepository->find($id);
        if (!$permission) {
            $this->flashMessenger()
                 ->setNamespace(self::FM_NS)
                 ->addErrorMessage(sprintf(
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
                $entityManager->persist($permission);
                $entityManager->flush();

                $this->flashMessenger()
                    ->setNamespace(self::FM_NS)
                    ->addSuccessMessage(sprintf(
                        $translator->translate("Permission '%s' was successfully updated.", self::TRANSLATOR_TEXT_DOMAIN),
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
        $entityManager = $this->getEntityManager();
        $id            = (int) $this->params()->fromRoute('id', 0);
        $translator    = $this->getServiceLocator()->get('Translator');

        $permission = $this->getEntityManager()->find($this->entities['Permission'], $id);
        if (!$permission) {
            $this->flashMessenger()
                 ->setNamespace(self::FM_NS)
                 ->addErrorMessage(sprintf(
                     $translator->translate("Permission does not found. '#%d'", self::TRANSLATOR_TEXT_DOMAIN),
                     $id
                 ));

            return $this->redirect()->toRoute('zfcadmin/user/permission');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $delete = $request->getPost('delete');
            if ($delete == 'Yes') {
                $entityManager->remove($permission);
                $entityManager->flush();

                $this->flashMessenger()
                     ->setNamespace(self::FM_NS)
                     ->addSuccessMessage(sprintf(
                         $translator->translate("Permission '%s' was successfully deleted.", self::TRANSLATOR_TEXT_DOMAIN),
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
     * @return PermissionAdminController
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get permissionForm
     *
     * @return Form
     */
    public function getPermissionForm()
    {
        if (!$this->permissionForm instanceof Form) {
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
        if (!$this->permissionSearchForm instanceof Form) {
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
