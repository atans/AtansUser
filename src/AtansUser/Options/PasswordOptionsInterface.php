<?php
namespace AtansUser\Options;

interface PasswordOptionsInterface
{
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
}