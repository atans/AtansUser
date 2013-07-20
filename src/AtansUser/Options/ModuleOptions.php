<?php
namespace AtansUser\Options;

use AtansUser\Entity\User;
use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    RegistrationInterface
{
    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * @var bool
     */
    protected $enableRegistration = true;

    /**
     * @var array
     */
    protected $userDefaultRoles = array('member');

    /**
     * @var string
     */
    protected $userDefaultStatus = User::STATUS_ACTIVE;

    /**
     * Set enable user registartion
     *
     * @param  bool $enableRegistration
     * @return ModuleOptions
     */
    public function setEnableRegistration($enableRegistration)
    {
        $this->enableRegistration = $enableRegistration;
        return $this;
    }

    /**
     * Get enable user registartion
     *
     * @return bool
     */
    public function getEnableRegistration()
    {
        return $this->enableRegistration;
    }

    /**
     * Set user default roles
     *
     * @param  array $userDefaultRoles
     * @return ModuleOptions
     */
    public function setUserDefaultRoles(array $userDefaultRoles)
    {
        $this->userDefaultRoles = $userDefaultRoles;
        return $this;
    }

    /**
     * Get user default roles
     *
     * @return array
     */
    public function getUserDefaultRoles()
    {
        return $this->userDefaultRoles;
    }

    /**
     * Set user default status
     *
     * @param  string $userDefaultStatus
     * @return ModuleOptions
     */
    public function setUserDefaultStatus($userDefaultStatus)
    {
        $this->userDefaultStatus = $userDefaultStatus;
        return $this;
    }

    /**
     * Get user default status
     *
     * @return string
     */
    public function getUserDefaultStatus()
    {
        return $this->userDefaultStatus;
    }
}
