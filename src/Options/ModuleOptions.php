<?php
namespace AtansUser\Options;

use AtansUser\Entity\User;
use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions implements
    AtansUserInterface,
    RegistrationOptionsInterface,
    UserOptionsInterface
{
    /**
     * @var string
     */
    protected $objectManagerName = 'doctrine.entitymanager.orm_default';

    /**
     * @var int
     */
    protected $userAdminCountPerPage = 10;

    /**
     * @var int
     */
    protected $roleAdminCountPerPage = 10;

    /**
     * @var int
     */
    protected $permissionAdminCountPerPage = 10;

    /**
     * @var bool
     */
    protected $enableRegistration = true;

    /**
     * @var array
     */
    protected $userDefaultRoles = array();

    /**
     * @var string
     */
    protected $userDefaultStatus = User::STATUS_ACTIVE;

    /**
     * @var bool
     */
    protected $loginAfterRegistration = true;

    /**
     * @var array
     */
    protected $authIdentityFields = array('username', 'email');

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
     * @var bool
     */
    protected $useRedirectParameterIfPresent = true;

    /**
     * @var int
     */
    protected $passwordCost = 14;

    /**
     * @var bool
     */
    protected $enableUserStatus = true;

    /**
     * @var array
     */
    protected $allowedLoginStatuses = array(User::STATUS_ACTIVE);

    /**
     * default entities settings
     *
     * @var array
     */
    protected $entities = [
        'User'       => 'AtansUser\Entity\User',
        'Role'       => 'AtansUser\Entity\Role',
        'Permission' => 'AtansUser\Entity\Permission',
    ];

    /**
     * Set objectManager
     *
     * @param  string $objectManagerName
     * @return ModuleOptions
     */
    public function setObjectManagerName($objectManagerName)
    {
        $this->objectManagerName = $objectManagerName;
        return $this;
    }

    /**
     * Get objectManagerName
     *
     * @return string
     */
    public function getObjectManagerName()
    {
        return $this->objectManagerName;
    }

    /**
     * Get userAdminCountPerPage
     *
     * @return int
     */
    public function getUserAdminCountPerPage()
    {
        return $this->userAdminCountPerPage;
    }

    /**
     * Set userAdminCountPerPage
     *
     * @param  int $userAdminCountPerPage
     * @return ModuleOptions
     */
    public function setUserAdminCountPerPage($userAdminCountPerPage)
    {
        $this->userAdminCountPerPage = (int) $userAdminCountPerPage;
        return $this;
    }

    /**
     * Get roleAdminCountPerPage
     *
     * @return int
     */
    public function getRoleAdminCountPerPage()
    {
        return $this->roleAdminCountPerPage;
    }

    /**
     * Set roleAdminCountPerPage
     *
     * @param  int $roleAdminCountPerPage
     * @return ModuleOptions
     */
    public function setRoleAdminCountPerPage($roleAdminCountPerPage)
    {
        $this->roleAdminCountPerPage = (int) $roleAdminCountPerPage;
        return $this;
    }

    /**
     * Get permissionAdminCountPerPage
     *
     * @return int
     */
    public function getPermissionAdminCountPerPage()
    {
        return $this->permissionAdminCountPerPage;
    }

    /**
     * Set permissionAdminCountPerPage
     *
     * @param  int $permissionAdminCountPerPage
     * @return ModuleOptions
     */
    public function setPermissionAdminCountPerPage($permissionAdminCountPerPage)
    {
        $this->permissionAdminCountPerPage = (int) $permissionAdminCountPerPage;
        return $this;
    }

    /**
     * Set enable user registration
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
     * Get enable user registration
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
     * Set loginAfterRegistration
     *
     * @param  boolean $loginAfterRegistration
     * @return ModuleOptions
     */
    public function setLoginAfterRegistration($loginAfterRegistration)
    {
        $this->loginAfterRegistration = $loginAfterRegistration;

        return $this;
    }

    /**
     * Get loginAfterRegistration
     *
     * @return boolean
     */
    public function getLoginAfterRegistration()
    {
        return $this->loginAfterRegistration;
    }

    /**
     * Set authIdentityFields
     *
     * @param  array $authIdentityFields
     * @return ModuleOptions
     */
    public function setAuthIdentityFields($authIdentityFields)
    {
        $this->authIdentityFields = $authIdentityFields;

        return $this;
    }

    /**
     * Get authIdentityFields
     *
     * @return array
     */
    public function getAuthIdentityFields()
    {
        return $this->authIdentityFields;
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
     * Set entities
     *
     * @param  $entities
     * @return ModuleOptions
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;
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
     * @return string
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

    /**
     * Set useRedirectParameterIfPresent
     *
     * @param  boolean $useRedirectParameterIfPresent
     * @return ModuleOptions
     */
    public function setUseRedirectParameterIfPresent($useRedirectParameterIfPresent)
    {
        $this->useRedirectParameterIfPresent = $useRedirectParameterIfPresent;
        return $this;
    }

    /**
     * Get useRedirectParameterIfPresent
     *
     * @return boolean
     */
    public function getUseRedirectParameterIfPresent()
    {
        return $this->useRedirectParameterIfPresent;
    }

    /**
     * Set passwordCost
     *
     * @param  int $passwordCost
     * @return ModuleOptions
     */
    public function setPasswordCost($passwordCost)
    {
        $this->passwordCost = $passwordCost;
        return $this;
    }

    /**
     * Get passwordCost
     *
     * @return int
     */
    public function getPasswordCost()
    {
        return $this->passwordCost;
    }

    /**
     * Set enableUserStatus
     *
     * @param  boolean $enableUserStatus
     * @return ModuleOptions
     */
    public function setEnableUserStatus($enableUserStatus)
    {
        $this->enableUserStatus = $enableUserStatus;

        return $this;
    }

    /**
     * Get enableUserStatus
     *
     * @return boolean
     */
    public function getEnableUserStatus()
    {
        return $this->enableUserStatus;
    }

    /**
     * Set allowedLoginStatuses
     *
     * @param  array $allowedLoginStatuses
     * @return ModuleOptions
     */
    public function setAllowedLoginStatuses($allowedLoginStatuses)
    {
        $this->allowedLoginStatuses = $allowedLoginStatuses;

        return $this;
    }

    /**
     * Get allowedLoginStatuses
     *
     * @return array
     */
    public function getAllowedLoginStatuses()
    {
        return $this->allowedLoginStatuses;
    }

    /**
     * Get entities
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
