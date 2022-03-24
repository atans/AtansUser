<?php
namespace AtansUser\Options;

interface AtansUserInterface
{
    /**
     * Set objectManagerName
     *
     * @param  string $objectManagerName
     * @return ModuleOptions
     */
    public function setObjectManagerName($objectManagerName);

    /**
     * Get objectManagerName
     *
     * @return string
     */
    public function getObjectManagerName();

    /**
     * Set userAdminCountPerPage
     *
     * @param  int $userAdminCountPerPage
     * @return ModuleOptions
     */
    public function setUserAdminCountPerPage($userAdminCountPerPage);

    /**
     * Set enable user registration
     *
     * @param  bool $enableRegistration
     * @return ModuleOptions
     */
    public function setEnableRegistration($enableRegistration);

    /**
     * Get roleAdminCountPerPage
     *
     * @return int
     */
    public function getRoleAdminCountPerPage();

    /**
     * Set roleAdminCountPerPage
     *
     * @param  int $roleAdminCountPerPage
     * @return ModuleOptions
     */
    public function setRoleAdminCountPerPage($roleAdminCountPerPage);

    /**
     * Get permissionAdminCountPerPage
     *
     * @return int
     */
    public function getPermissionAdminCountPerPage();

    /**
     * Set permissionAdminCountPerPage
     *
     * @param  int $permissionAdminCountPerPage
     * @return ModuleOptions
     */
    public function setPermissionAdminCountPerPage($permissionAdminCountPerPage);
}