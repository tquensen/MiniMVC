<?php

class Core_Task_Controller extends MiniMVC_Controller
{

    public function clearCacheAction()
    {
        $status = $this->clearDirectory(BASEPATH . 'cache', true, true);
        return ($status === true) ? 'cache directory cleared!' : 'error: clearing cache directory failed: '.$status;
    }

    protected function clearDirectory($dir, $recursive = false, $root = false)
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
                $status = $this->clearDirectory($dir.'/'.$file);
                if ($status !== true) {
                    return $status;
                }
                if (!$root) {
                    if (!@rmdir($dir.'/'.$file)) {
                        return 'rmdir failed for directory "'.$dir.'/'.$file.'"';
                    }
                }
            }
        }
        return true;
    }

}