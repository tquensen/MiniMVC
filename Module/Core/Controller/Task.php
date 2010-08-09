<?php

class Core_Task_Controller extends MiniMVC_Controller
{

    public function clearCacheAction()
    {
        $status = $this->clearDirectory(BASEPATH . 'cache', true);
        return ($status === true) ? 'cache directory cleared!' : 'error: clearing cache directory failed: '.$status;
    }

    public function createLinksAction()
    {
       if (!file_exists(BASEPATH.'Cache/public')) {
           mkdir(BASEPATH.'Cache/public');
       }
       if (!file_exists(WEBPATH.'cache')) {
           echo 'Creating Link "'.WEBPATH.'cache" pointing to "'.BASEPATH.'Cache/public"'."\n";
                symlink(BASEPATH.'Cache/public', WEBPATH.'cache');
       }
       if (!file_exists(WEBPATH.'app')) {
           mkdir(WEBPATH.'app');
       }
       foreach ($this->registry->settings->apps as $app => $appData) {
            if (file_exists(APPPATH.$app.'/Web') && !file_exists(WEBPATH.'app/'.$app)) {
                echo 'Creating Link "'.WEBPATH.'app/'.$app.'" pointing to "'.APPPATH.$app.'/Web'.'"'."\n";
                symlink(APPPATH.$app.'/Web', WEBPATH.'app/'.$app);
            }
       }
       if (!file_exists(WEBPATH.'module')) {
           mkdir(WEBPATH.'module');
       }
       foreach ($this->registry->settings->modules as $module) {
            if (file_exists(MODULEPATH.$module.'/Web') && !file_exists(WEBPATH.'module/'.$module)) {
                echo 'Creating Link "'.WEBPATH.'module/'.$module.'" pointing to "'.MODULEPATH.$module.'/Web'.'"'."\n";
                symlink(MODULEPATH.$module.'/Web', WEBPATH.'module/'.$module);
            }
       }
       return 'Symlinks wurrden erstellt!';
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