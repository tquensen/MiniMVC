<?php
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