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
use ZfcRbac\Exception\UnauthorizedException;
use ZfcRbac\Service\AuthorizationService;

class UserAdmin extends EventProvider implements ServiceLocatorAwareInterface
{
    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var AuthorizationService
     */
    protected $authorizationService;

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
     * @param  User $user
     * @return UserAdmin
     * @throws UnauthorizedException
     */
    public function add(User $user)
    {
        if (! $this->getAuthorizationService()->isGranted('atansuser.admin.user.add')) {
            throw new UnauthorizedException();
        }

        $user->setCreated(new DateTime());

        $bcrypt = new Bcrypt();
        $bcrypt->setCost($this->getOptions()->getPasswordCost());
        $user->setPassword($bcrypt->create($user->getPassword()));

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $user));
        $this->getObjectManager()->persist($user);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $user));

        return $this;
    }

    /**
     * Edit user
     *
     * @param User $user
     * @param string $newPassword
     * @return UserAdmin
     * @throws UnauthorizedException
     */
    public function edit(User $user, $newPassword)
    {
        if (! $this->getAuthorizationService()->isGranted('atansuser.admin.user.edit')) {
            throw new UnauthorizedException();
        }

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

        return $this;
    }

    /**
     * Delete user
     *
     * @param  User $user
     * @return UserAdmin
     * @throws UnauthorizedException
     */
    public function delete(User $user)
    {
        if (! $this->getAuthorizationService()->isGranted('atansuser.admin.user.delete')) {
            throw new UnauthorizedException();
        }

        $this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $user));
        $this->getObjectManager()->remove($user);
        $this->getObjectManager()->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $user));

        return $this;
    }

    /**
     * Get authorizationService
     *
     * @return AuthorizationService
     */
    public function getAuthorizationService()
    {
        if (! $this->authorizationService instanceof AuthorizationService) {
            $this->setAuthorizationService($this->getServiceLocator()->get('ZfcRbac\Service\AuthorizationService'));
        }
        return $this->authorizationService;
    }

    /**
     * Set authorizationService
     *
     * @param  AuthorizationService $authorizationService
     * @return UserAdmin
     */
    public function setAuthorizationService(AuthorizationService $authorizationService)
    {
        $this->authorizationService = $authorizationService;
        return $this;
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
