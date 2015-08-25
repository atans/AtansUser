<?php
namespace AtansUser\Options;

interface UserOptionsInterface
{
    /**
     * set auth identity fields
     *
     * @param array $authIdentityFields
     * @return ModuleOptions
     */
    public function setAuthIdentityFields($authIdentityFields);

    /**
     * get auth identity fields
     *
     * @return array
     */
    public function getAuthIdentityFields();

    /**
     * Set login redirect route
     *
     * @param  $LoginRedirectRoute
     * @return ModuleOptions
     */
    public function setLoginRedirectRoute($LoginRedirectRoute);

    /**
     * Get login redirect route
     *
     * @return string
     */
    public function getLoginRedirectRoute();

    /**
     * Set logout redirect route
     *
     * @param  string $logoutRedirectRoute
     * @return ModuleOptions
     */
    public function setLogoutRedirectRoute($logoutRedirectRoute);

    /**
     * Get logout redirect route
     *
     * @return string
     */
    public function getLogoutRedirectRoute();

    /**
     * Set user index template
     *
     * @param  string $userIndexTemplate
     * @return ModuleOptions
     */
    public function setUserIndexTemplate($userIndexTemplate);

    /**
     * Get user index template
     *
     * @return string
     */
    public function getUserIndexTemplate();

    /**
     * set use redirect param if present
     *
     * @param bool $useRedirectParameterIfPresent
     */
    public function setUseRedirectParameterIfPresent($useRedirectParameterIfPresent);

    /**
     * get use redirect param if present
     *
     * @return bool
     */
    public function getUseRedirectParameterIfPresent();

    /**
     * set password cost
     *
     * @param  int $passwordCost
     * @return ModuleOptions
     */
    public function setPasswordCost($passwordCost);

    /**
     * get password cost
     *
     * @return int
     */
    public function getPasswordCost();

    /**
     * Set enable user status
     *
     * @param  bool $enableUserStatus
     * @return ModuleOptions
     */
    public function setEnableUserStatus($enableUserStatus);

    /**
     * Get enable user status
     *
     * @return bool
     */
    public function getEnableUserStatus();

    /**
     * Set allowed login statuses
     *
     * @param  array $allowedLoginStatuses
     * @return mixed
     */
    public function setAllowedLoginStatuses($allowedLoginStatuses);

    /**
     * Get allowed login statuses
     *
     * @return array
     */
    public function getAllowedLoginStatuses();
}
