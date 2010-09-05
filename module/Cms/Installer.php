<?php

class Cms_Installer extends MiniMVC_Installer
{

    public function install($installedVersion)
    {
        try
        {
            $cms = new CmsArticleTable();
            $cms->install($installedVersion);
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
            $cms = new CmsArticleTable();
            $cms->uninstall($installedVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
