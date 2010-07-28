<?php
/**
 * MiniMVC_Installer is the base class for module installers
 */
class MiniMVC_Installer
{
    protected $message = 'This Module doesn´t require installation.';

    public function install()
    {
        return true;
    }

    public function getMessage()
    {
        return $this->message;
    }
}