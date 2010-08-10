<?php

class Blubb_Installer extends MiniMVC_Installer
{

    public function install()
    {
        BlubberTable::get()->install();
        return true;
    }

    public function uninstall()
    {
        BlubberTable::get()->uninstall();
        return true;
    }

}
