<?php

class Mongo_Install_Controller extends MiniMVC_Controller
{

    public function modelAction($params)
    {
        if (!$params['model']) {
            return 'No model specified!' . "\n";
        }

        $className = $params['model'] . 'Repository';

        if (!class_exists($className)) {
            return 'Model repository class "' . $className . '" does not exist!' . "\n";
        }

        $class = new $className;

        if ($params['type'] == 'install') {
            try {
                $status = $class->install($params['fromVersion']);
                if ($status !== true && $status !== null) {
                    return 'An error occurred: ' . $status;
                } else {
                    return 'Model ' . $params['model'] . ' was installed successfully' . ($params['fromVersion'] ? ' from ' . $params['fromVersion'] : '') . ($params['toVersion'] ? ' to ' . $params['toVersion'] : '') . '!';
                }
            } catch (Exception $e) {
                return 'An error occurred: ' . $e->getMessage();
            }
        } elseif ($params['type'] == 'uninstall') {
            try {
                $status = $class->uninstall($params['fromVersion']);
                if ($status !== true && $status !== null) {
                    return 'An error occurred: ' . $status;
                } else {
                    return 'Model ' . $params['model'] . ' was uninstalled successfully' . ($params['fromVersion'] ? ' from ' . $params['fromVersion'] : '') . ($params['toVersion'] ? ' to ' . $params['toVersion'] : '') . '!';
                }
            } catch (Exception $e) {
                return 'An error occurred: ' . $e->getMessage();
            }
        }
    }

}

