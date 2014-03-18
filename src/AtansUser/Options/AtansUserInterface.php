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
}