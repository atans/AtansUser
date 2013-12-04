<?php
namespace AtansUser\Service;

use AtansUser\Entity\Role;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\EventManager\EventProvider;

class RoleAdmin extends EventProvider implements ServiceLocatorAwareInterface
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
     * Add role
     *
     * @param Role $role
     * @return bool
     */
    public function add(Role $role)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('role' => $role));
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
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
        $this->getEntityManager()->persist($role);
        $this->getEntityManager()->flush();
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
        $this->getEntityManager()->remove($role);
        $this->getEntityManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('role' => $role));

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
