<?php
namespace AtansUser\Service;

use AtansUser\Entity\Permission;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\EventManager\EventProvider;

class PermissionAdmin extends EventProvider implements ServiceLocatorAwareInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Add permission
     *
     * @param Permission $permission
     * @return bool
     */
    public function add(Permission $permission)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('permission' => $permission));
        $this->getEntityManager()->persist($permission);
        $this->getEntityManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('permission' => $permission));

        return true;
    }

    /**
     * Edit permission
     *
     * @param Permission $permission
     * @return bool
     */
    public function edit(Permission $permission)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('permission' => $permission));
        $this->getEntityManager()->persist($permission);
        $this->getEntityManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('permission' => $permission));

        return true;
    }

    /**
     * Delete permission
     *
     * @param Permission $permission
     * @return bool
     */
    public function delete(Permission $permission)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('permission' => $permission));
        $this->getEntityManager()->remove($permission);
        $this->getEntityManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('permission' => $permission));

        return true;
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
     * @return UserAdmin
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
