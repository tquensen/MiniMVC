<?php

class Dev_Install_Controller extends MiniMVC_Controller
{

    public function moduleAction($params)
    {
        if (!$params['module']) {
            return 'No module specified!' . "\n";
        }

        if (!file_exists(MODULEPATH . $params['module'])) {
            return 'Module '.$params['module'].' does not exist!' . "\n";
        }

        $class = $params['module'] . '_Installer';

        if (!class_exists($class)) {
            return 'Module '.$params['module'].' has no installer!' . "\n";
        }
        
        $installer = new $class();
        if ($params['type'] == 'install') {
            if ($installer->install($params['fromVersion'], $params['toVersion'])) {
                return 'Module '.$params['module'].' was installed successfully' . (($params['fromVersion'] || $params['toVersion']) ? ' from '.$params['fromVersion'].' to '.$params['toVersion'].'!' : '!');
            } else {
                return $installer->getMessage();
            }
        } elseif ($params['type'] == 'uninstall') {
            if ($installer->uninstall($params['fromVersion'], $params['toVersion'])) {
                return 'Module '.$params['module'].' was uninstalled successfully' . (($params['fromVersion'] || $params['toVersion']) ? ' from '.$params['fromVersion'].' to '.$params['toVersion'].'!' : '!');
            } else {
                return $installer->getMessage();
            }
        }
    }

    public function modelAction($params)
    {
        if (!$params['model']) {
            return 'No model specified!' . "\n";
        }

        $className = $params['model'].'Table';

        if (!class_exists($className)) {
            return 'Model table class "'.$className.'" does not exist!' . "\n";
        }

        $class = call_user_func($className.'::getInstance');
        
        if ($params['type'] == 'install') {
            try {
                $status = $class->install($params['fromVersion']);
                if ($status !== true && $status !== null) {
                     return 'An error occurred: '.$status;
                } else {
                     return 'Model '.$params['model'].' was installed successfully' . (($params['fromVersion'] || $params['toVersion']) ? ' from '.$params['fromVersion'].' to '.$params['toVersion'].'!' : '!');
                }
            } catch (Exception $e) {
                return 'An error occurred: '.$e->getMessage();
            }
        } elseif ($params['type'] == 'uninstall') {
            try {
                $status = $class->uninstall($params['fromVersion']);
                if ($status !== true && $status !== null) {
                     return 'An error occurred: '.$status;
                } else {
                     return 'Model '.$params['model'].' was uninstalled successfully' . (($params['fromVersion'] || $params['toVersion']) ? ' from '.$params['fromVersion'].' to '.$params['toVersion'].'!' : '!');
                }
            } catch (Exception $e) {
                return 'An error occurred: '.$e->getMessage();
            }
        }
    }

}

