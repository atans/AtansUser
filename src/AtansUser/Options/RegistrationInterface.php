<?php
namespace AtansUser\Options;

interface RegistrationInterface
{
    /**
     * Set enable user registartion
     *
     * @param  bool $enableRegistration
     * @return ModuleOptions
     */
    public function setEnableRegistration($enableRegistration);

    /**
     * Get enable user registartion
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
}