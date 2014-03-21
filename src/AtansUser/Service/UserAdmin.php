<?php
namespace AtansUser\Service;

use AtansUser\Entity\User;
use AtansUser\Options\ModuleOptions;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcBase\EventManager\EventProvider;

class UserAdmin extends EventProvider implements ServiceLocatorAwareInterface
{
    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var EntityManagerInterface
     */
    protected $objectManager;

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

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());
        $user->setPassword($bcrypt->create($user->getPassword()));

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $user));
        $this->getObjectManager()->persist($user);
        $this->getObjectManager()->flush();
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
        $this->getObjectManager()->persist($user);
        $this->getObjectManager()->flush();
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
        $this->getObjectManager()->remove($user);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $user));

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
