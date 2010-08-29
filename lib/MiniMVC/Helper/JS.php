<?php

class Helper_JS extends MiniMVC_Helper
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
        
        $route = $this->registry->settings->get('runtime/currentRoute');
        $format = $this->registry->template->getFormat();
        $layout = $this->registry->template->getLayout();

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
            if (!isset($file['format']) || $file['format'] == 'html') {
                $file['format'] = null;
            }
            if (!is_array($file['format']) && $file['format'] != 'all' && ($file['format'] != $format)) {
                unset($files[$filekey]);
            } elseif(is_array($file['format']) && !in_array($format ? $format : 'html', $file['format'])) {
                unset($files[$filekey]);
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

    public function getHtml()
    {
        return $this->registry->helper->Partial->get('js', array('files' => $this->get(), 'inlineFiles' => $this->getInlineFiles(), 'vars' => $this->getVars()), $this->module);
    }

    public function addFile($file, $module = null, $app = null)
    {
        if (!$app) {
            $app = $this->registry->settings->get('runtime/currentApp');
        }
        $data = array();

        $data['url'] = $this->staticHelper->get('js/' . $file, $module, $app);

        $this->additionalFiles[$module . '/' . $file] = $data;
    }

    public function addInlineFile($file, $module = null, $app = null)
    {
        if (!$app) {
            $app = $this->registry->settings->get('runtime/currentApp');
        }

        $file = null;
        if ($module) {
            if (file_exists(APPPATH.$app.'/web/'.$module.'/js/'.$file['file'])) {
                $file = APPPATH.$app.'/web/'.$module.'/js/'.$file['file'];
            } else {
                $file = MODULEPATH.$module.'/web/js/'.$file['file'];
            }
        } else {
            $file = APPPATH.$app.'/web/js/'.$file['file'];
        }

        if (file_exists($file)) {
            $this->inlineFiles[$module . '/' . $file] = file_get_contents($file);
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
        if ($cache = $this->registry->settings->get('view/jsCached')) {
            return $cache;
        }

        $files = $this->registry->settings->get('view/js', array());
        $preparedFiles = array();
        foreach ($files as $file) {
            $data = array();
            if (is_string($file)) {
                $file = array('file' => $file);
            } elseif (!isset($file['file'])) {
                continue;
            }
            $module = (isset($file['module'])) ? $file['module'] : null;
            $app = (isset($file['app'])) ? $file['app'] : $this->registry->settings->get('runtime/currentApp');

            if ($module) {
                if (file_exists(APPPATH.$app.'/web/'.$module.'/js/'.$file['file'])) {
                    $data['file'] = APPPATH.$app.'/web/'.$module.'/js/'.$file['file'];
                } else {
                    $data['file'] = MODULEPATH.$module.'/web/js/'.$file['file'];
                }
            } else {
                $data['file'] = APPPATH.$app.'/web/js/'.$file['file'];
            }
            $data['url'] = $this->staticHelper->get('js/' . $file['file'], $module, $app);
            $data['combine'] = (isset($file['combine'])) ? $file['combine'] : true;
            $preparedFiles[$module . '/' . $file['file']] = $data;
        }

        $combinedFiles = $this->combineFiles($preparedFiles);
        $this->registry->settings->set('view/jsCached', $combinedFiles);

        return $combinedFiles;
    }

    public function combineFiles($files, $app = null, $environment = null)
    {

        $app = ($app) ? $app : $this->registry->settings->get('runtime/currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('runtime/currentEnvironment');

        $uncombinedBefore = array();
        $uncombinedAfter = array();

        $combinedFound = false;
        $data = array();
        foreach ($files as $file) {
            if ($file['combine']) {
                $combinedFound = true;
                $data[] = file_get_contents($file);
            } else {
                if ($combinedFound) {
                    $uncombinedAfter[] = $file;
                } else {
                    $uncombinedBefore[] = $file;
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
                'url' => $this->staticHelper->get('cache/'.$filename, null, $app)
            );
        }
 
        return array_merge($uncombinedBefore, $newFiles, $uncombinedAfter);
    }

}