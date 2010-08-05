<?php

class User_Installer extends MiniMVC_Installer
{

    public function install($installedVersion)
    {
        try
        {
            $user = new UserTable();
            $user->install($installedVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

    public function uninstall($installedVersion)
    {
        try
        {
            $user = new UserTable();
            $user->uninstall($installedVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
