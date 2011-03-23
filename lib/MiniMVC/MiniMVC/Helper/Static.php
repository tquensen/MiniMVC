<?php

class Helper_Static extends MiniMVC_Helper
{
	public function get($file, $module = null, $app = null)
	{
        if ($module === null)
        {
            $module = $this->module;
        }

        if (strpos($file, '?')) {
            list($file, $queryStr) = explode('?', $file, 2);
        } else {
            $queryStr = false;
        }

        $app = ($app) ? $app : $this->registry->settings->get('currentApp');
        $theme = $this->registry->layout->getTheme();

        $filekey = $app.'_'.$theme.'_'.$module.'_'.str_replace('/', '__', $file);

        if ($cache = $this->registry->cache->get('staticCached/'.$filekey))
        {
            return $queryStr ? $cache . '?' . $queryStr : $cache;
        }

        $prefixHash = $this->registry->settings->get('view/static/prefixHash', false);

        if ($baseurl = $this->registry->settings->get('apps/'.$app.'/baseurlStatic')) {
            if (is_array($baseurl)) {
                $baseurl = array_values($baseurl);
                $baseurl = $baseurl[hexdec(substr(md5($file), 0, 6)) % count($baseurl)];
            }
        }
        else {
            $baseurl = $this->registry->settings->get('apps/'.$app.'/baseurl', '');
        }


        if ($theme && $module !== null && file_exists(APPPATH.$app.'/web/'.$theme.'/'.$module.'/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(APPPATH.$app.'/web/'.$theme.'/'.$module.'/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.'app/'.$app.'/'.$theme.'/'.$module.'/'.$file;
        }
        elseif ($theme && $module !== null && file_exists(WEBPATH.$theme.'/'.$module.'/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(WEBPATH.$theme.'/'.$module.'/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.$theme.'/'.$module.'/'.$file;
        }
        elseif ($theme && $module !== null && file_exists(THEMEPATH.$theme.'web/'.$module.'/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(THEMEPATH.$theme.'web/'.$module.'/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.'theme/'.$theme.'/'.$module.'/'.$file;
        }
        elseif ($module !== null && file_exists(APPPATH.$app.'/web/'.$module.'/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(APPPATH.$app.'/web/'.$module.'/'.$file)) . '_.' . $ext;
            }           
            $url = $baseurl.'app/'.$app.'/'.$module.'/'.$file;
        }
        elseif ($module !== null && file_exists(WEBPATH.$module.'/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(WEBPATH.$module.'/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.$module.'/'.$file;
        }
        elseif ($module !== null && file_exists(MODULEPATH.$module.'/web/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(MODULEPATH.$module.'/web/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.'module/'.$module.'/'.$file;
        }
        elseif ($theme && file_exists(APPPATH.$app.'/web/'.$theme.'/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(APPPATH.$app.'/web/'.$theme.'/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.'app/'.$app.'/'.$theme.'/'.$file;
        }
        elseif ($theme && file_exists(WEBPATH.$theme.'/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(WEBPATH.$theme.'/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.$theme.'/'.$file;
        }
        elseif ($theme && file_exists(THEMEPATH.$theme.'web/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(THEMEPATH.$theme.'web/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.'theme/'.$theme.'/'.$file;
        }
        elseif (file_exists(APPPATH.$app.'/web/'.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(APPPATH.$app.'/web/'.$file)) . '_.' . $ext;
            }
            $url = $baseurl.'app/'.$app.'/'.$file;
        }
        elseif (file_exists(WEBPATH.$file))
        {
            if ($prefixHash) {
                $parts = explode('.', $file);
                $ext = array_pop($parts);
                $file = implode('.', $parts) . '_' . md5(filemtime(WEBPATH.$file)) . '_.' . $ext;
            }
            $url = $baseurl.$file;
        }
        else
        {
            $url = $baseurl.$file;
        }

        $this->registry->cache->set('staticCached/'.$filekey, $url);

        return $queryStr ? $url . '?' . $queryStr : $url;
	}
}