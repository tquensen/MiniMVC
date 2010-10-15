<?php

class Helper_Static extends MiniMVC_Helper
{
	public function get($file, $module = null, $app = null)
	{
        if ($module === null)
        {
            $module = $this->module;
        }
        $app = ($app) ? $app : $this->registry->settings->get('runtime/currentApp');

        if ($cache = $this->registry->settings->get('view/staticCached/'.$app.'_'.$module.'_'.str_replace('/', '__', $file)))
        {
            return $cache;
        }

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
            $url = $baseurl.'app/'.$app.'/'.$module.'/'.$file;
        }
        elseif ($module !== null && file_exists(MODULEPATH.$module.'/web/'.$file))
        {
            $url = $baseurl.'module/'.$module.'/'.$file;
        }
        elseif (file_exists(APPPATH.$app.'/web/'.$file))
        {
            $url = $baseurl.'app/'.$app.'/'.$file;
        }
        else
        {
            $url = $baseurl.$file;
        }

        $this->registry->settings->set('view/staticCached/'.$app.'_'.$module.'_'.str_replace('/', '__', $file), $url);

        return $url;
	}
}