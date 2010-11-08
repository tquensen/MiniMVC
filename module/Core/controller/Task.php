<?php

class Core_Task_Controller extends MiniMVC_Controller
{
    public function clearCacheAction($params)
    {
        $apps = !empty($params['rebuild']) ? $this->registry->settings->get('apps', array()) : array();
        $status = $this->clearDirectory(CACHEPATH, true);
        if ($status === true && $apps) {
            $cache = $this->registry->settings->get('useCache');
            $this->registry->settings->set('runtime/useCache', true);
            $envs = !empty($params['rebuild']) ? array_map('trim', explode(',', $params['rebuild'])) : array();
            foreach ($apps as $app => $appdata) {
                foreach ($envs as $env) {
                    if (!$env) {
                        continue;
                    }
                    $this->registry->settings->scanConfigFiles($app, $env);
                }
            }
            $this->registry->settings->set('runtime/useCache', $cache);
        }
        return ($status === true) ? 'cache directory cleared!' : 'error: clearing cache directory failed: '.$status;
    }

    public function createLinksAction()
    {
       if (!file_exists(CACHEPATH.'public')) {
           mkdir(CACHEPATH.'public');
       }
       if (!file_exists(WEBPATH.'cache')) {
           echo 'Creating Link "'.WEBPATH.'cache" pointing to "'.CACHEPATH.'public"'."\n";
           symlink(CACHEPATH.'public', WEBPATH.'cache');
       }
       if (!file_exists(WEBPATH.'app')) {
           mkdir(WEBPATH.'app');
       }
       foreach ($this->registry->settings->get('apps', array()) as $app => $appData) {
            if (file_exists(APPPATH.$app.'/web') && !file_exists(WEBPATH.'app/'.$app)) {
                echo 'Creating Link "'.WEBPATH.'app/'.$app.'" pointing to "'.APPPATH.$app.'/web'.'"'."\n";
                symlink(APPPATH.$app.'/web', WEBPATH.'app/'.$app);
            }
       }
       if (!file_exists(WEBPATH.'module')) {
           mkdir(WEBPATH.'module');
       }
       foreach ($this->registry->settings->get('modules', array()) as $module) {
            if (file_exists(MODULEPATH.$module.'/web') && !file_exists(WEBPATH.'module/'.$module)) {
                echo 'Creating Link "'.WEBPATH.'module/'.$module.'" pointing to "'.MODULEPATH.$module.'/web'.'"'."\n";
                symlink(MODULEPATH.$module.'/web', WEBPATH.'module/'.$module);
            }
       }
       return 'Symlinks wurden erstellt!';
    }

    protected function clearDirectory($dir, $recursive = false, $level = 1)
    {
        if (!is_dir($dir) || !is_writable($dir)) {
            return 'no write/delete permissons for directory "'.$dir.'"';
        }
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_file($dir.'/'.$file)) {
                if (!is_writable($dir.'/'.$file)) {
                   return 'no write permisson for file "'.$dir.'/'.$file.'"';
                }
                if (!@unlink($dir.'/'.$file)) {
                    return 'unlink failed for file "'.$dir.'/'.$file.'"';
                }
            } elseif (is_dir($dir.'/'.$file) && $recursive) {
                if ($level == 1 && $file == 'public') {
                    continue;
                }
                $status = $this->clearDirectory($dir.'/'.$file, $recursive, $level + 1);
                if ($status !== true) {
                    return $status;
                }
                if (!@rmdir($dir.'/'.$file)) {
                    return 'rmdir failed for directory "'.$dir.'/'.$file.'"';
                }
            }
        }
        return true;
    }

}