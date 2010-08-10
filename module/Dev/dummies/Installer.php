<?php

class MODULE_Installer extends MiniMVC_Installer
{

    public function install($installedVersion)
    {
        try
        {
            $MODLC = new MODULETable();
            $MODLC->install($installedVersion);
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
            $MODLC = new MODULETable();
            $MODLC->uninstall($installedVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
