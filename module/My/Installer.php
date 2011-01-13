<?php

class Example_Installer extends MiniMVC_Installer
{

    public function install($installedVersion, $targetVersion)
    {
        try
        {
            //$example = new ExampleTable();
            //$example->install($installedVersion, $targetVersion);
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
            //$example = new ExampleTable();
            //$example->uninstall($installedVersion, $targetVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
