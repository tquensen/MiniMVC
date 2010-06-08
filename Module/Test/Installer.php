<?php

class Module_Test_Installer extends MiniMVC_Installer
{

    public function install()
    {
        $migration = new Doctrine_Migration(dirname(__FILE__).'/Lib/Migrations');
        $migration->setCurrentVersion(0);
        $migration->migrate();
        return true;
    }

    public function uninstall()
    {
        $migration = new Doctrine_Migration(dirname(__FILE__).'/Lib/Migrations');
        $migration->setCurrentVersion(count(scandir(dirname(__FILE__).'/Lib/Migrations')) - 2);
        $migration->migrate(0);
        return true;
    }

}
