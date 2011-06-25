<?php

class Helper_Js extends MiniMVC_Helper
{
    protected $staticHelper = null;
    protected $additionalFiles = array();
    protected $inlineFiles = array();
    protected $vars = array();

    public function __construct()
    {
        parent::__construct();
        $this->staticHelper = $this->registry->helper->static;
    }

    public function get()
    {
        $files = $this->prepareFiles();
        
        $route = $this->registry->settings->get('currentRoute');
        $format = $this->registry->layout->getFormat();
        $layout = $this->registry->layout->getLayout();
        $theme = $this->registry->layout->getTheme();

        foreach ($files as $filekey => $file) {
            if (isset($file['show']) && $file['show']) {
                if (is_string($file['show']) && $file['show'] != $route) {
                    unset($files[$filekey]);
                } elseif(is_array($file['show']) && !in_array($route, $file['show'])) {
                    unset($files[$filekey]);
                }
            }
            if (isset($file['hide']) && $file['hide']) {
                if (is_string($file['hide']) && $file['hide'] == $route) {
                    unset($files[$filekey]);
                } elseif(is_array($file['hide']) && in_array($route, $file['hide'])) {
                    unset($files[$filekey]);
                }
            }
            if (empty($file['format']) || $file['format'] == 'default') {
                $file['format'] = null;
            }
            if (!is_array($file['format']) && $file['format'] != 'all' && ($file['format'] != $format)) {
                unset($files[$filekey]);
            } elseif(is_array($file['format']) && !in_array($format ? $format : 'default', $file['format'])) {
                unset($files[$filekey]);
            }

            if (isset($file['theme'])) {
                if (!is_array($file['theme']) && $file['theme'] != 'all' && ($file['theme'] != $theme)) {
                    unset($files[$filekey]);
                } elseif(is_array($file['theme']) && !in_array($theme ? $theme : 'default', $file['theme'])) {
                    unset($files[$filekey]);
                }
            }
            
            if (isset($file['layout']) && $file['layout']) {
                if (is_string($file['layout']) && $file['layout'] != 'all' && $file['layout'] != $layout) {
                    unset($files[$filekey]);
                } elseif(is_array($file['layout']) && !in_array($layout, $file['layout'])) {
                    unset($files[$filekey]);
                }
            }
        }
        return array_merge($files, $this->additionalFiles);
    }

    public function getInlineFiles()
    {
        return $this->inlineFiles;
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function getHtml($module = null, $partial = 'js')
    {
        $cache = $this->registry->helper->cache->get(array('name' => 'js', 'module' => $module, 'partial' => $partial), array('css'), true);
        if ($return = $cache->load()) {
            return $return;
        }
        $return = $this->registry->helper->partial->get($partial, array('files' => $this->get(), 'inlineFiles' => $this->getInlineFiles(), 'vars' => $this->getVars()), $module ? $module : $this->module);
        $cache->save($return);
        return $return;
    }

    public function addFile($file, $module = null, $app = null)
    {
        if (!$app) {
            $app = $this->registry->settings->get('currentApp');
        }
        $data = array();

        $data['url'] = $this->staticHelper->get('js/' . $file, $module, $app);

        $this->additionalFiles[$module . '/' . $file] = $data;
    }

    public function addInlineFile($filename, $module = null, $app = null)
    {
        if (!$app) {
            $app = $this->registry->settings->get('currentApp');
        }
        $theme = $this->registry->layout->getTheme();

        $file = null;
        if ($module) {
            if ($theme && file_exists(APPPATH.$app.'/web/'.$theme.'/'.$module.'/js/'.$filename)) {
                $file = APPPATH.$app.'/web/'.$theme.'/'.$module.'/js/'.$filename;
            } elseif ($theme && file_exists(WEBPATH.$theme.'/'.$module.'/js/'.$filename)) {
                $file = WEBPATH.$theme.'/'.$module.'/js/'.$filename;
            } elseif ($theme && file_exists(THEMEPATH.$theme.'/web/'.$module.'/js/'.$filename)) {
                $file = THEMEPATH.$theme.'/web/'.$module.'/js/'.$filename;
            } elseif (file_exists(APPPATH.$app.'/web/'.$module.'/js/'.$filename)) {
                $file = APPPATH.$app.'/web/'.$module.'/js/'.$filename;
            } elseif (file_exists(WEBPATH.$module.'/js/'.$filename)) {
                $file = WEBPATH.$module.'/js/'.$filename;
            } else {
                $file = MODULEPATH.$module.'/web/js/'.$filename;
            }
        } else {
            if ($theme && file_exists(APPPATH.$app.'/web/'.$theme.'/js/'.$filename)) {
                $file = APPPATH.$app.'/web/'.$theme.'/js/'.$filename;
            } elseif ($theme && file_exists(WEBPATH.$theme.'/js/'.$filename)) {
                $file = WEBPATH.$theme.'/js/'.$filename;
            } elseif ($theme && file_exists(THEMEPATH.$theme.'/web/js/'.$filename)) {
                $file = THEMEPATH.$theme.'/web/js/'.$filename;
            } elseif (file_exists(APPPATH.$app.'/web/js/'.$filename)) {
                $file = APPPATH.$app.'/web/js/'.$filename;
            } else {
                $file = WEBPATH.'/js/'.$filename;
            }
        }

        if (file_exists($file)) {
            $this->inlineFiles[$module . '/' . $filename] = file_get_contents($file);
        }
    }

    public function addVar($key, $value, $literal = false)
    {
        $subkeys = explode('.', $key);
        if (count($subkeys) > 1) {
            array_pop($subkeys);
            $partialkey = array_shift($subkeys);
            $this->vars[$partialkey] = 'minimvc.'.$partialkey.' || {}';
            foreach ($subkeys as $subkey) {
                $partialkey .= '.'.$subkey;
                if (!isset($this->vars[$partialkey])) {
                    $this->vars[$partialkey] = 'minimvc.'.$partialkey.' || {}';
                }
            }
        }
        $this->vars[$key] = $literal ? $value : json_encode($value);
    }

    public function prepareFiles()
    {
        $theme = $this->registry->layout->getTheme();
        if (null !== ($cache = $this->registry->cache->get('jsCached_'.$theme))) {
            return $cache;
        }
        
        if ($theme) {
            $MiniMVC_view = $this->registry->settings->get('view', array());
            $currentApp = $this->registry->settings->get('currentApp');
            if (file_exists(THEMEPATH.$theme.'/theme.php')) {
                include THEMEPATH.$theme.'/theme.php';
            }
            if (file_exists(DATAPATH.'settings/theme.php')) {
                include DATAPATH.'settings/theme.php';
            }
            if ($currentApp && file_exists(APPPATH.$currentApp.'settings/theme.php')) {
                include APPPATH.$currentApp.'settings/theme.php';
            }
            $files = isset($MiniMVC_view['js']) ? $MiniMVC_view['js'] : array();
        } else {
            $files = $this->registry->settings->get('view/js', array());
        }
        
        $preparedFiles = array();
        foreach ($files as $file) {
            $data = array();
            if (is_string($file)) {
                $file = array('file' => $file);
            } elseif (!isset($file['file'])) {
                continue;
            }
            $module = (isset($file['module'])) ? $file['module'] : null;
            $app = (isset($file['app'])) ? $file['app'] : $this->registry->settings->get('currentApp');

            if ($module) {
                if ($theme && file_exists(APPPATH.$app.'/web/'.$theme.'/'.$module.'/js/'.$file['file'])) {
                    $data['file'] = APPPATH.$app.'/web/'.$theme.'/'.$module.'/js/'.$file['file'];
                } elseif ($theme && file_exists(WEBPATH.$theme.'/'.$module.'/js/'.$file['file'])) {
                    $data['file'] = WEBPATH.$theme.'/'.$module.'/js/'.$file['file'];
                } elseif ($theme && file_exists(THEMEPATH.$theme.'/web/'.$module.'/js/'.$file['file'])) {
                    $data['file'] = THEMEPATH.$theme.'/web/'.$module.'/js/'.$file['file'];
                } elseif (file_exists(APPPATH.$app.'/web/'.$module.'/js/'.$file['file'])) {
                    $data['file'] = APPPATH.$app.'/web/'.$module.'/js/'.$file['file'];
                } elseif (file_exists(WEBPATH.$module.'/js/'.$file['file'])) {
                    $data['file'] = WEBPATH.$module.'/js/'.$file['file'];
                } else {
                    $data['file'] = MODULEPATH.$module.'/web/js/'.$file['file'];
                }
            } else {
                if ($theme && file_exists(APPPATH.$app.'/web/'.$theme.'/js/'.$file['file'])) {
                    $data['file'] = APPPATH.$app.'/web/'.$theme.'/js/'.$file['file'];
                } elseif ($theme && file_exists(WEBPATH.$theme.'/js/'.$file['file'])) {
                    $data['file'] = WEBPATH.$theme.'/js/'.$file['file'];
                } elseif ($theme && file_exists(THEMEPATH.$theme.'/web/js/'.$file['file'])) {
                    $data['file'] = THEMEPATH.$theme.'/web/js/'.$file['file'];
                } elseif (file_exists(APPPATH.$app.'/web/js/'.$file['file'])) {
                    $data['file'] = APPPATH.$app.'/web/js/'.$file['file'];
                } else {
                    $data['file'] = WEBPATH.'/js/'.$file['file'];
                }
            }
            $data['url'] = $this->staticHelper->get('js/' . $file['file'], $module, $app);
            $data['combine'] = (isset($file['combine'])) ? $file['combine'] : true;
            $preparedFiles[$module . '/' . $file['file']] = $data;
        }

        $combinedFiles = $this->combineFiles($preparedFiles);
        $this->registry->cache->set('jsCached_'.$theme, $combinedFiles);

        return $combinedFiles;
    }

    public function combineFiles($files)
    {

        $uncombinedBefore = array();
        $uncombinedAfter = array();

        $combinedFound = false;
        $data = array();
        foreach ($files as $file) {
            if ($file['combine']) {
                $combinedFound = true;
                $data[] = file_get_contents($file['file']);
            } else {
                if ($combinedFound) {
                    $uncombinedAfter[] = $file['file'];
                } else {
                    $uncombinedBefore[] = $file['file'];
                }
            }
            
        }


        $newFiles = array();
        if (!is_dir(CACHEPATH.'public')) {
            mkdir (CACHEPATH.'public');
        }

        if (count($data)) {
            $content = implode("\n\n", $data);
            $fileHash = md5($content);
            $filename = 'js_'.$fileHash.'.js';

            file_put_contents(CACHEPATH.'public/'.$filename, $content);
            $newFiles[$fileHash] = array(
                'url' => $this->staticHelper->get('cache/'.$filename, null)
            );
        }
 
        return array_merge($uncombinedBefore, $newFiles, $uncombinedAfter);
    }

}