<?php
namespace AtansUser\Service;

use AtansUser\Options\ModuleOptions;
use Doctrine\ORM\EntityManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\EventManager\EventProvider;
use Rbac\Role\HierarchicalRoleInterface;

class RoleAdmin extends EventProvider implements ServiceLocatorAwareInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $objectManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Add role
     *
     * @param Rbac\Role\HierarchicalRoleInterface $role
     * @return bool
     */
    public function add(HierarchicalRoleInterface $role)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('role' => $role));
        $this->getObjectManager()->persist($role);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('role' => $role));

        return true;
    }

    /**
     * Edit role
     *
     * @param Rbac\Role\HierarchicalRoleInterface $role
     * @return bool
     */
    public function edit(HierarchicalRoleInterface $role)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('role' => $role));
        $this->getObjectManager()->persist($role);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('role' => $role));

        return true;
    }

    /**
     * Delete role
     *
     * @param  Rbac\Role\HierarchicalRoleInterface $role
     * @return bool
     */
    public function delete(HierarchicalRoleInterface $role)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('role' => $role));
        $this->getObjectManager()->remove($role);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('role' => $role));

        return true;
    }

    /**
     * Get entityManager
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
     * Set entityManager
     *
     * @param  EntityManagerInterface $objectManager
     * @return UserAdmin
     */
    public function setObjectManager(EntityManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
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
     * @param  ModuleOptions $options
     * @return RoleAdmin
     */
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}
