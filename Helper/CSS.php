<?php

class Helper_CSS extends MiniMVC_Helper
{
    protected $staticHelper = null;
    protected $additionalFiles = array();

    public function __construct()
    {
        parent::__construct();
        $this->staticHelper = $this->registry->helper->static;
    }

    public function get()
    {
        return array_merge($this->prepareFiles(), $this->additionalFiles);
    }

    public function getHtml()
    {
        return $this->registry->helper->Partial->get('css', array('files' => array_merge($this->prepareFiles(), $this->additionalFiles)), $this->module);
    }

    public function addFile($file, $module = null, $media = 'screen', $app = null)
    {
        if (!$app) {
            $app = $this->registry->settings->currentApp;
        }
        $data = array();

        $data['url'] = $this->staticHelper->get('css/' . $file, $module, $app);
        $data['media'] = $media;

        $this->additionalFiles[$module . '/' . $file] = $data;
    }

    public function prepareFiles()
    {
        $view = $this->registry->settings->view;

        if (isset($view['cssCached'])) {
            return $view['cssCached'];
        }

        $files = (isset($view['css'])) ? $view['css'] : array();
        $files = array_merge($files, $this->additionalFiles);
        $preparedFiles = array();
        foreach ($files as $file) {
            $data = array();
            if (is_string($file)) {
                $file = array('file' => $file);
            } elseif (!isset($file['file'])) {
                continue;
            }
            $module = (isset($file['module'])) ? $file['module'] : null;
            $app = (isset($file['app'])) ? $file['app'] : $this->registry->settings->currentApp;

            $data['url'] = $this->staticHelper->get('css/' . $file['file'], $module, $app);
            $data['media'] = (isset($file['media'])) ? $file['media'] : 'screen';
            $data['combine'] = (isset($file['combine'])) ? $file['combine'] : true;
            $preparedFiles[$module . '/' . $file['file']] = $data;
        }

        $combinedFiles = $this->combineFiles($preparedFiles, $app);
        $view['cssCached'] = $combinedFiles;
        $this->registry->settings->saveToCache('view', $view);

        return $combinedFiles;
    }

    public function combineFiles($files, $app = null, $environment = null)
    {

        $app = ($app) ? $app : $this->registry->settings->currentApp;
        $environment = ($environment) ? $environment : $this->registry->settings->currentEnvironment;

        if (isset($this->registry->settings->apps[$app]['baseurlStatic'])) {
            $baseurl = $this->registry->settings->apps[$app]['baseurlStatic'];
        } else {
            $baseurl = (isset($this->registry->settings->apps[$app]['baseurl'])) ? $this->registry->settings->apps[$app]['baseurl'] : '';
        }

        $uncombinedBefore = array();
        $uncombinedAfter = array();
        $medias = array();

        $combinedFound = false;
        foreach ($files as $file) {
            if ($file['combine']) {
                $combinedFound = true;
                $relativePath = str_replace($baseurl, '', $file['url']); //relative path from MiniMVC root
                $urlPrefix = $baseurl.'../' . dirname($relativePath) . '/'; //relative url from cache to original css file folder
                $filePath = BASEPATH . $relativePath;
                $data = $this->parseFile($filePath, $urlPrefix, $app, $environment);
                foreach (explode(',', $file['media']) as $media) {
                    $medias[trim($media)][] = $data;
                }
            } else {
                if ($combinedFound) {
                    $uncombinedAfter[] = $file;
                } else {
                    $uncombinedBefore[] = $file;
                }
            }
            
        }

        $newFiles = array();
        foreach ($medias as $media => $mediaFiles) {
            $content = implode("\n\n", $mediaFiles);
            $fileHash = md5($content);
            $filename = 'css_'.$app.'_'.$environment.'_'.$media.'_'.$fileHash.'.css';
            $newFiles[] = array(
                'url' => $baseurl.'cache/'.$filename,
                'media' => $media
            );
            file_put_contents(BASEPATH.'cache/'.$filename, $content);
        }
        return array_merge($uncombinedBefore, $newFiles, $uncombinedAfter);
    }

    public function parseFile($file, $urlPrefix, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->currentApp;
        $environment = ($environment) ? $environment : $this->registry->settings->currentEnvironment;
        $activeModules = $this->registry->settings->get('modules', $app, $environment);

        if (!is_file($file) || !is_readable($file)) {
            return '';
        }

        $data = file_get_contents($file);
        $regex = '#url\s*\(\s*([^\)]*)\s*\)#i';
        if (preg_match_all($regex, $data, $matches, PREG_SET_ORDER)) {
            $search = array();
            $replace = array();
            foreach ($matches as $match) {
                if (!isset($match[1]) || !$match[1] || substr($match[1], 0, 1) == '/' || strstr($match[1], ':') !== false) {
                    continue;
                }
                $pathDirty = explode('/', $urlPrefix . trim($match[1], "\r\n \"'"));
                $pathNew = array();
                foreach ($pathDirty as $k => $v) {
                    if ($v == '.') {
                        continue;
                    }
                    if ($v == '..' && isset($pathNew[$k - 1])) {
                        unset($pathNew[$k - 1]);
                        continue;
                    }
                    $pathNew[$k] = $v;
                }
                $search[] = $match[0];
                $pathNew = implode('/', $pathNew);
                if (preg_match('#Module/(\w*)/Web/(.*)$#', $pathNew, $moduleMatch)) {
                    $pathNew = $this->staticHelper->get($moduleMatch[2], $moduleMatch[1], $app);
                } elseif (preg_match('#App/(\w*)/Web/(.*)$#', $pathNew, $moduleMatch)) {
                    $tmp = explode('/', $moduleMatch[2]);
                    if (in_array($tmp[0], $activeModules)) {
                        $module = array_shift($tmp);
                        $moduleMatch[2] = implode('/', $tmp);
                    } else {
                        $module = null;
                    }
                    $pathNew = $this->staticHelper->get($moduleMatch[2], $module, $moduleMatch[1]);
                } elseif (preg_match('#Web/(.*)$#', $pathNew, $moduleMatch)) {
                    $pathNew = $this->staticHelper->get($moduleMatch[1], null, $app);
                }
                $replace[] = 'url("' . $pathNew . '")';
            }
            $data = str_replace($search, $replace, $data);
        }
        return $data;
    }

}