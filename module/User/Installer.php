<?php

class User_Installer extends MiniMVC_Installer
{

    public function install($installedVersion, $targetVersion)
    {
        try
        {
            $user = new UserTable();
            $user->install($installedVersion, $targetVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

    public function uninstall($installedVersion, $targetVersion)
    {
        try
        {
            $user = new UserTable();
            $user->uninstall($installedVersion, $targetVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
