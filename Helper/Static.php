<?php

class Helper_Static extends MiniMVC_Helper
{	
	public function get($file, $module = null, $app = null)
	{
        $view = $this->registry->settings->view;

        if ($module === null)
        {
            $module = $this->module;
        }
        $app = ($app) ? $app : $this->registry->settings->currentApp;

        if (isset($view['staticCached'][$app.'_'.$module.'_'.$file]))
        {
            return $view['staticCached'][$app.'_'.$module.'_'.$file];
        }

        if (isset($this->registry->settings->apps[$app]['baseurlStatic'])) {
            $baseurl = $this->registry->settings->apps[$app]['baseurlStatic'];
            if (is_array($baseurl)) {
                $baseurl = array_values($baseurl);
                $baseurl = $baseurl[hexdec(substr(md5($file), 0, 6)) % count($baseurl)];
            }
        }
        else {
            $baseurl = (isset($this->registry->settings->apps[$app]['baseurl'])) ? $this->registry->settings->apps[$app]['baseurl'] : '';
        }

        if ($module !== null && file_exists(APPPATH.$app.'/Web/'.$module.'/'.$file))
        {
            $url = $baseurl.'app/'.$app.'/'.$module.'/'.$file;
        }
        elseif ($module !== null && file_exists(MODULEPATH.$module.'/Web/'.$file))
        {
            $url = $baseurl.'module/'.$module.'/'.$file;
        }
        elseif (file_exists(APPPATH.$app.'/Web/'.$file))
        {
            $url = $baseurl.'app/'.$app.'/'.$file;
        }
        elseif (file_exists(BASEPATH.'Web/'.$file))
        {
            $url = $baseurl.$file;
        }
        else
        {
            $url = $file;
        }

        $view['staticCached'][$app.'_'.$module.'_'.$file] = $url;
        $this->registry->settings->saveToCache('view', $view);

        return $url;
	}
}