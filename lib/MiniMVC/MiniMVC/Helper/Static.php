<?php

class Helper_Static extends MiniMVC_Helper
{
	public function get($file, $module = null, $app = null)
	{
        if ($module === null)
        {
            $module = $this->module;
        }
        $app = ($app) ? $app : $this->registry->settings->get('currentApp');

        $filekey = $app.'_'.$module.'_'.str_replace('/', '__', $file);

        $cache = $this->registry->cache->get('staticCached');
        if (isset($cache[$filekey]))
        {
            return $cache[$filekey];
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

        if ($module !== null && file_exists(APPPATH.$app.'/web/'.$module.'/'.$file))
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

        $this->registry->cache->set('staticCached', array($filekey => $url), true);

        return $url;
	}
}