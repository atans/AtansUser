<?php
namespace AtansUser\Controller;

use AtansUser\Entity\Role;
use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;

class RoleAdminController extends AbstractActionController
{
    /**
     * Flash messenger namespace
     *
     * @var string
     */
    const FM_NS = 'atansuser-role-admin-index';

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
        'Role' => 'AtansUser\Entity\Role',
    );

    /**
     * @var Form
     */
    protected $roleForm;

    /**
     * @var Form
     */
    protected $roleSearchForm;

    public function indexAction()
    {
        $userRepository = $this->getEntityManager()->getRepository($this->entities['Role']);
        $request        = $this->getRequest();

        $data = array(
            'page'   => $request->getQuery('page', 1),
            'size'   => $request->getQuery('size', 10),
            'query'  => $request->getQuery('query', ''),
            'order'  => $request->getQuery('order', 'DESC'),
        );

        $form = $this->getRoleSearchForm();
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

        $role = new Role();

        $form = $this->getRoleForm();
        $form->bind($role);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $entityManager->persist($role);
                $entityManager->flush();

                $this->flashMessenger()
                    ->setNamespace(self::FM_NS)
                    ->addMessage(sprintf(
                        $translator->translate("Role '%s' was successfully created.", self::TRANSLATOR_TEXT_DOMAIN),
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
        $entityManager = $this->getEntityManager();
        $id            = (int) $this->params()->fromRoute('id', 0);
        $translator    = $this->getServiceLocator()->get('Translator');


        $role = $entityManager->find($this->entities['Role'], $id);
        if (!$role) {
            $this->flashMessenger()
                 ->setNamespace(self::FM_NS)
                 ->addMessage(sprintf(
                    $translator->translate("Role does not found. '#%d'", self::TRANSLATOR_TEXT_DOMAIN),
                    $id
                ));

            return $this->redirect()->toRoute('zfcadmin/user/role');
        }

        $form = $this->getRoleForm();
        $form->bind($role);

        $parentRoleProxy = $form->get('parentRole')->getProxy();
        $parentRoleProxy->setIsMethod(true)
                        ->setFindMethod(array(
                            'name'   => 'findAllRoleWithoutId',
                            'params' => array('id' => $id),
                        ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $entityManager->persist($role);
                $entityManager->flush();

                $this->flashMessenger()
                     ->setNamespace(self::FM_NS)
                     ->addSuccessMessage(sprintf(
                         $translator->translate("Role '%s' was successfully updated.", self::TRANSLATOR_TEXT_DOMAIN),
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
        $entityManager = $this->getEntityManager();
        $id            = (int) $this->params()->fromRoute('id', 0);
        $translator    = $this->getServiceLocator()->get('Translator');

        $role = $this->getEntityManager()->find($this->entities['Role'], $id);
        if (!$role) {
            $this->flashMessenger()
                ->setNamespace(self::FM_NS)
                ->addMessage(sprintf(
                    $translator->translate("Role does not found. '#%d'", self::TRANSLATOR_TEXT_DOMAIN),
                    $id
                ));

            return $this->redirect()->toRoute('zfcadmin/user/role');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $delete = $request->getPost('delete');
            if ($delete == 'Yes') {
                $entityManager->remove($role);
                $entityManager->flush();

                $this->flashMessenger()
                    ->setNamespace(self::FM_NS)
                    ->addMessage(sprintf(
                        $translator->translate("Role '%s' was successfully deleted.", self::TRANSLATOR_TEXT_DOMAIN),
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
     * @return RoleController
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * Get roleForm
     *
     * @return Form
     */
    public function getRoleForm()
    {
        if (!$this->roleForm instanceof Form) {
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
     * Get roleSearchForm
     *
     * @return Form
     */
    public function getRoleSearchForm()
    {
        if (!$this->roleSearchForm instanceof Form) {
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
    public function setRoleSearchForm($roleSearchForm)
    {
        $this->roleSearchForm = $roleSearchForm;

        return $this;
    }
}
