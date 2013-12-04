<?php
namespace AtansUser\Service;

use AtansUser\Entity\User;
use AtansUser\Options\ModuleOptions;
use DateTime;
use Doctrine\ORM\EntityManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\EventManager\EventProvider;

class UserAdmin extends EventProvider implements ServiceLocatorAwareInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Add user
     *
     * @param User $user
     * @return bool
     */
    public function add(User $user)
    {
        $user->setCreated(new DateTime());

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $user));
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $user));

        return true;
    }

    /**
     * Edit user
     *
     * @param User $user
     * @param string $newPassword
     * @return bool
     */
    public function edit(User $user, $newPassword)
    {
        // Update new password
        if (strlen($newPassword) > 0) {
            $bcrypt = new Bcrypt();
            $bcrypt->setCost($this->getOptions()->getPasswordCost());
            $user->setPassword($bcrypt->create($newPassword));
        }

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $user));
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $user));

        return true;
    }

    /**
     * Delete user
     *
     * @param  User $user
     * @return bool
     */
    public function delete(User $user)
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $user));
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $user));

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
     * @return UserAdmin
     */
    public function setOptions($options)
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
