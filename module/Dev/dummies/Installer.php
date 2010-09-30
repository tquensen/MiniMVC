<?php

class MODULE_Installer extends MiniMVC_Installer
{

    public function install($installedVersion, $targetVersion)
    {
        try
        {
            //$MODLC = new MODULETable();
            //$MODLC->install($installedVersion, $targetVersion);
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
            //$MODLC = new MODULETable();
            //$MODLC->uninstall($installedVersion, $targetVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
