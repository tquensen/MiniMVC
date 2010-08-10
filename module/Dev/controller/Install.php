<?php

class Dev_Install_Controller extends MiniMVC_Controller
{

    public function moduleAction($params)
    {
        if (!$params['module']) {
            return 'Kein Modul angegeben!' . "\n";
        }

        if (!file_exists(MODULEPATH . $params['module'])) {
            return 'Modul existiert nicht!' . "\n";
        }

        $class = $params['module'] . '_Installer';

        if (!class_exists($class)) {
            return 'Modul hat keinen Installer!' . "\n";
        }
        
        $installer = new $class();
        if ($params['type'] == 'install') {
            return $installer->install($params['fromVersion']) ? 'Modul wurde installiert!' : $installer->getMessage();
        } elseif ($params['type'] == 'uninstall') {
            return $installer->uninstall($params['fromVersion']) ? 'Modul wurde deinstalliert!' : $installer->getMessage();
        }
    }

    public function modelAction($params)
    {
        if (!$params['model']) {
            return 'Kein Model angegeben!' . "\n";
        }

        $className = $params['model'].'Table';

        if (!class_exists($className)) {
            return 'Model Table Klasse "'.$className.'" existiert nicht!' . "\n";
        }

        $class = call_user_func($className.'::getInstance');
        
        if ($params['type'] == 'install') {
            try {
                $status = $class->install($params['fromVersion']);
                if ($status !== true && $status !== null) {
                     return 'Es gab einen Fehler beim Installieren: '.$status;
                } else {
                     return  'Model wurde installiert!';
                }
            } catch (Exception $e) {
                return 'Es gab einen Fehler beim Installieren: '.$e->getMessage();
            }
        } elseif ($params['type'] == 'uninstall') {
            try {
                $status = $class->uninstall($params['fromVersion']);
                if ($status !== true && $status !== null) {
                     return 'Es gab einen Fehler beim Deinstallieren: '.$status;
                } else {
                     return  'Model wurde deinstalliert!';
                }
            } catch (Exception $e) {
                return 'Es gab einen Fehler beim Deinstallieren: '.$e->getMessage();
            }
        }
    }

}

