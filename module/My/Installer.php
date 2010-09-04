<?php

class My_Installer extends MiniMVC_Installer
{

    public function install($installedVersion)
    {
        try
        {
            $my = new MyTable();
            $my->install($installedVersion);
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
            $my = new MyTable();
            $my->uninstall($installedVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
