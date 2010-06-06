<?php

class Module_User_Installer extends MiniMVC_Installer
{

    public function install()
    {
        try
        {
            $user = new User();
            $user->installModel();
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
