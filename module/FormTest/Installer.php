<?php

class FormTest_Installer extends MiniMVC_Installer
{

    public function install($installedVersion, $targetVersion)
    {
        try
        {
            //$formTest = new FormTestTable();
            //$formTest->install($installedVersion, $targetVersion);
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
            //$formTest = new FormTestTable();
            //$formTest->uninstall($installedVersion, $targetVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
