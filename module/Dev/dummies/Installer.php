<?php

class MODULE_Installer extends MiniMVC_Installer
{

    public function install($installedVersion, $targetVersion)
    {
        try
        {
            //$CONTROLLERLCFIRST = new CONTROLLERTable();
            //$CONTROLLERLCFIRST->install($installedVersion, $targetVersion);
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
            //$CONTROLLERLCFIRST = new CONTROLLERTable();
            //$CONTROLLERLCFIRST->uninstall($installedVersion, $targetVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
