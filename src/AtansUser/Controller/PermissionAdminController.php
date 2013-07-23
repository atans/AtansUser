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
    protected $permissionAddForm;

    /**
     * @var Form
     */
    protected $permissionEditForm;

    public function indexAction()
    {
        $permissionRepository = $this->getEntityManager()->getRepository($this->entities['Permission']);

        $returns = array(
            'permissions'   => $permissionRepository->findAll(),
            'flashMessages' => null,
        );

        $flashMessenger = $this->flashMessenger()->setNamespace(self::FM_NS);
        if ($flashMessages = $flashMessenger->getMessages()) {
            $returns['flashMessages'] = $flashMessages;
        }

        return $returns;
    }

    public function addAction()
    {
        $entityManager = $this->getEntityManager();
        $translator    = $this->getServiceLocator()->get('Translator');

        $permission = new Permission();

        $form = $this->getPermissionAddForm();
        $form->bind($permission);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $entityManager->persist($permission);
                $entityManager->flush();

                $this->flashMessenger()
                    ->setNamespace(self::FM_NS)
                    ->addMessage(sprintf(
                        $translator->translate("新增權限成功 '%s'"),
                        $permission->getName()
                    ));

                return $this->redirect()->toRoute('zfcadmin/permission');
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
                 ->addMessage(sprintf(
                     $translator->translate("找不到權限 '%d'"),
                     $id
                  ));

            return $this->redirect()->toRoute('zfcadmin/permission');
        }

        $form = $this->getPermissionEditForm();
        $form->bind($permission);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                if ($permissionRepository->noNameExists($permission->getName(), $permission->getId())) {
                    $entityManager->persist($permission);
                    $entityManager->flush();

                    $this->flashMessenger()
                        ->setNamespace(self::FM_NS)
                        ->addMessage(sprintf(
                            $translator->translate("修改權限成功 '%s'"),
                            $permission->getName()
                        ));

                    return $this->redirect()->toRoute('zfcadmin/permission');
                } else {
                    $form->get('name')->setMessages(array(sprintf(
                        $translator->translate("權限'%s'已存在"),
                        $permission->getName()
                    )));
                }

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
                 ->addMessage(sprintf(
                     $translator->translate("找不到權限 '%d'"),
                     $id
                 ));

            return $this->redirect()->toRoute('zfcadmin/permission');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $delete = $request->getPost('delete');
            if ($delete == 'Yes') {
                $entityManager->remove($permission);
                $entityManager->flush();

                $this->flashMessenger()
                     ->setNamespace(self::FM_NS)
                     ->addMessage(sprintf(
                         $translator->translate("刪除權限成功 '%s'"),
                         $permission->getName()
                     ));

                return $this->redirect()->toRoute('zfcadmin/permission');
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
     * @return PermissionController
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get permissionAddForm
     *
     * @return Form
     */
    public function getPermissionAddForm()
    {
        if (!$this->permissionAddForm instanceof Form) {
            $this->setPermissionAddForm($this->getServiceLocator()->get('atansuser_permission_add_form'));
        }
        return $this->permissionAddForm;
    }

    /**
     * Set permissionAddForm
     *
     * @param  Form $permissionAddForm
     * @return PermissionController
     */
    public function setPermissionAddForm(Form $permissionAddForm)
    {
        $this->permissionAddForm = $permissionAddForm;
        return $this;
    }

    /**
     * Get permissionEditForm
     *
     * @return Form
     */
    public function getPermissionEditForm()
    {
        if (!$this->permissionEditForm instanceof Form) {
            $this->setPermissionEditForm($this->getServiceLocator()->get('atansuser_permission_edit_form'));
        }
        return $this->permissionEditForm;
    }

    /**
     * Set permissionEditForm
     *
     * @param  Form $permissionEditForm
     * @return PermissionController
     */
    public function setPermissionEditForm(Form $permissionEditForm)
    {
        $this->permissionEditForm = $permissionEditForm;
        return $this;
    }
}
