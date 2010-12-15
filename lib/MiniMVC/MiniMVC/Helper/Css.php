<?php

class Helper_Css extends MiniMVC_Helper
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
        $files = $this->prepareFiles();

        $route = $this->registry->settings->get('currentRoute');
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

    public function getHtml($module = null, $partial = 'css')
    {
        return $this->registry->helper->partial->get($partial, array('files' => $this->get()), $module ? $module : $this->module);
    }

    public function addFile($file, $module = null, $media = 'screen', $app = null)
    {
        if (!$app) {
            $app = $this->registry->settings->get('currentApp');
        }
        $data = array();

        $data['url'] = $this->staticHelper->get('css/' . $file, $module, $app);
        $data['media'] = $media;
        $data['combine'] = false;

        $this->additionalFiles[$module . '/' . $file] = $data;
    }

    public function prepareFiles()
    {
        if (null !== ($cache = $this->registry->cache->get('cssCached'))) {
            return array_merge($cache, $this->additionalFiles);
        }

        $files = $this->registry->settings->get('view/css', array());
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

            $data['url'] = $this->staticHelper->get('css/' . $file['file'], $module, $app);
            $data['media'] = (isset($file['media'])) ? $file['media'] : 'screen';
            $data['combine'] = (isset($file['combine'])) ? $file['combine'] : true;
            $data['minify'] = (isset($file['minify'])) ? $file['minify'] : $data['combine'];
            $data['module'] = $module;
            $data['file'] = $file['file'];
            $preparedFiles[$app.'/'.$module . '/' . $file['file']] = $data;
        }

        $combinedFiles = $this->combineFiles($preparedFiles);
        $this->registry->cache->set('cssCached', $combinedFiles);

        return array_merge($combinedFiles, $this->additionalFiles);
    }

    public function combineFiles($files, $app = null, $environment = null)
    {

        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');


        $uncombinedBefore = array();
        $uncombinedAfter = array();
        $medias = array();

        $combinedFound = false;
        foreach ($files as $file) {
            if ($file['combine']) {
                $combinedFound = true;

                if ($module) {
                    if (file_exists(WEBPATH.'app/'.$app.'/'.$module.'/css/'.$file['file'])) {
                        $filePath = WEBPATH.'app/'.$app.'/'.$module.'/css/'.$file['file'];
                    } elseif (file_exists(WEBPATH.$module.'/css/'.$file['file'])) {
                        $filePath = WEBPATH.$module.'/css/'.$file['file'];
                    } else {
                        $filePath = WEBPATH.'module/'.$module.'/css/'.$file['file'];
                    }
                    $urlPrefix = 'module/'.$module.'/css/';
                } elseif (file_exists(WEBPATH.'app/'.$app.'/css/'.$file['file'])) {
                    $filePath = WEBPATH.'app/'.$app.'/css/'.$file['file'];
                    $urlPrefix = 'css/';
                } else {
                    $filePath = WEBPATH.'css/'.$file['file'];
                    $urlPrefix = 'css/';
                }

//                $relativePath = str_replace($baseurls, '', $file['url']); //relative path from web root
//                $urlPrefix = dirname($relativePath) . '/';
//                $filePath = $file['file'];
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
        if (!is_dir(CACHEPATH.'public')) {
            mkdir (CACHEPATH.'public');
        }
        foreach ($medias as $media => $mediaFiles) {
            $content = implode("\n\n", $mediaFiles);
            $fileHash = md5($content);
            $filename = 'css_'.$fileHash.'.css';

            if (!isset($newFiles[$fileHash])) {
                file_put_contents(CACHEPATH.'public/'.$filename, $content);
	            $newFiles[$fileHash] = array(
                    'file' => CACHEPATH.'public/'.$filename,
	                'url' => $this->staticHelper->get('cache/'.$filename, null, $app),
	                'media' => $media
	            );
            } else {
            	$newFiles[$fileHash]['media'] .= ', ' . $media;
            }
        }
        return array_merge($uncombinedBefore, $newFiles, $uncombinedAfter);
    }

    public function parseFile($file, $urlPrefix, $app = null, $environment = null)
    {
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $environment = ($environment) ? $environment : $this->registry->settings->get('currentEnvironment');
        $activeModules = $this->registry->settings->get('modules', null, $app, $environment);

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
                    if ($v == '..' && !empty($pathNew)) {
                        array_pop($pathNew);
                        //unset($pathNew[$k - 1]);
                        continue;
                    }
                    $pathNew[] = $v;
                }
                $search[] = $match[0];
                $pathNew = implode('/', $pathNew);
                if (preg_match('#^module/(\w*)/(.*)$#', $pathNew, $moduleMatch)) {
                    $pathNew = $this->staticHelper->get($moduleMatch[2], $moduleMatch[1], $app);
                } elseif (preg_match('#^app/(\w*)/(.*)$#', $pathNew, $moduleMatch)) {
                    $tmp = explode('/', $moduleMatch[2]);
                    if (in_array($tmp[0], $activeModules)) {
                        $module = array_shift($tmp);
                        $moduleMatch[2] = implode('/', $tmp);
                    } else {
                        $module = null;
                    }
                    $pathNew = $this->staticHelper->get($moduleMatch[2], $module, $moduleMatch[1]);
                } elseif (preg_match('#(.*)$#', $pathNew, $moduleMatch)) {
                    $tmp = explode('/', $moduleMatch[1]);
                    if (in_array($tmp[0], $activeModules)) {
                        $module = array_shift($tmp);
                        $moduleMatch[1] = implode('/', $tmp);
                    } else {
                        $module = null;
                    }
                    $pathNew = $this->staticHelper->get($moduleMatch[1], null, $app);
                }
                $replace[] = 'url("' . $pathNew . '")';
            }
            $data = str_replace($search, $replace, $data);
        }
        return $data;
    }

}