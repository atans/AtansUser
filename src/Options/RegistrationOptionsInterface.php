<?php
namespace AtansUser\Options;

interface RegistrationOptionsInterface
{
    /**
     * Set enable user registration
     *
     * @param  bool $enableRegistration
     * @return ModuleOptions
     */
    public function setEnableRegistration($enableRegistration);

    /**
     * Get enable user registration
     *
     * @return bool
     */
    public function getEnableRegistration();

    /**
     * Set user default roles
     *
     * @param  array $userDefaultRoles
     * @return ModuleOptions
     */
    public function setUserDefaultRoles(array $userDefaultRoles);

    /**
     * Get user default roles
     *
     * @return array
     */
    public function getUserDefaultRoles();

    /**
     * Set user default status
     *
     * @param  string $userDefaultStatus
     * @return ModuleOptions
     */
    public function setUserDefaultStatus($userDefaultStatus);

    /**
     * Get user default status
     *
     * @return string
     */
    public function getUserDefaultStatus();

    /**
     * set login after registration
     *
     * @param bool $loginAfterRegistration
     * @return ModuleOptions
     */
    public function setLoginAfterRegistration($loginAfterRegistration);

    /**
     * get login after registration
     *
     * @return bool
     */
    public function getLoginAfterRegistration();
}