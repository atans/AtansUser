<?php
namespace AtansUser\Options;

interface UserInterface
{
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
     * @return stirng
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
}
