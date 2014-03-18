<?php
namespace AtansUser\Service;

use AtansUser\Entity\Role;
use AtansUser\Options\ModuleOptions;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\EventManager\EventProvider;

class RoleAdmin extends EventProvider implements ServiceLocatorAwareInterface
{
    /**
     * @var EntityManager
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
     * @param Role $role
     * @return bool
     */
    public function add(Role $role)
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
     * @param Role $role
     * @return bool
     */
    public function edit(Role $role)
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
     * @param  Role $role
     * @return bool
     */
    public function delete(Role $role)
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
     * @return EntityManager
     */
    public function getObjectManager()
    {
        if (! $this->objectManager instanceof EntityManager) {
            $this->setObjectManager($this->getServiceLocator()->get($this->getOptions()->getObjectManagerName()));
        }
        return $this->objectManager;
    }

    /**
     * Set entityManager
     *
     * @param  EntityManager $objectManager
     * @return UserAdmin
     */
    public function setObjectManager(EntityManager $objectManager)
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
