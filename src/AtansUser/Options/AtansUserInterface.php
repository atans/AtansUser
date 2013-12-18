<?php

namespace AtansUser\Options;

interface AtansUserInterface
{
    /**
     * Set objectManager
     *
     * @param  string $objectManager
     * @return ModuleOptions
     */
    public function setObjectManager($objectManager);

    /**
     * Get objectManager
     *
     * @return string
     */
    public function getObjectManager();
}