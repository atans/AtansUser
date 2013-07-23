<?php
namespace AtansUser\Options;

use AtansUser\Entity\User;
use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    RegistrationInterface,
    UserInterface
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
     * @var string
     */
    protected $loginRedirectRoute = 'atansuser';

    /**
     * @var string
     */
    protected $logoutRedirectRoute = 'atansuser/login';

    /**
     * @var string
     */
    protected $userIndexTemplate = 'atans-user/user/index';

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

    /**
     * Set login redirect route
     *
     * @param  $LoginRedirectRoute
     * @return ModuleOptions
     */
    public function setLoginRedirectRoute($LoginRedirectRoute)
    {
        $this->loginRedirectRoute = $LoginRedirectRoute;
        return $this;
    }

    /**
     * Get login redirect route
     *
     * @return string
     */
    public function getLoginRedirectRoute()
    {
        return $this->loginRedirectRoute;
    }

    /**
     * Set logout redirect route
     *
     * @param  string $logoutRedirectRoute
     * @return ModuleOptions
     */
    public function setLogoutRedirectRoute($logoutRedirectRoute)
    {
        $this->logoutRedirectRoute = $logoutRedirectRoute;
        return $this;
    }

    /**
     * Get logout redirect route
     *
     * @return stirng
     */
    public function getLogoutRedirectRoute()
    {
        return $this->logoutRedirectRoute;
    }

    /**
     * Set user index template
     *
     * @param  string $userIndexTemplate
     * @return ModuleOptions
     */
    public function setUserIndexTemplate($userIndexTemplate)
    {
        $this->userIndexTemplate = $userIndexTemplate;
        return $this;
    }

    /**
     * Get user index template
     *
     * @return string
     */
    public function getUserIndexTemplate()
    {
        return $this->userIndexTemplate;
    }
}
