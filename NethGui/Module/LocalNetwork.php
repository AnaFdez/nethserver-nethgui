<?php

/**
 * NethGui
 *
 * @package Modules
 */

/**
 * TODO: describe class
 *
 * @package Modules
 */
final class NethGui_Module_LocalNetwork extends NethGui_Core_Module_Standard implements NethGui_Core_TopModuleInterface
{

    public function getTitle()
    {
        return "Local network";
    }

    public function getDescription()
    {
        return "Placeholder description.";
    }

    public function getParentMenuIdentifier()
    {
        return "Security";
    }

}