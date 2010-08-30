<?php

class Helper_Url extends MiniMVC_Helper
{	
	public function get($route, $parameter = array(), $app = null)
	{
		$app = ($app) ? $app : $this->registry->settings->get('runtime/currentApp');
		try
		{
			$routeData = $this->registry->dispatcher->getRoute($route, $parameter, $app);
		}
		catch (Exception $e)
		{
			return false;
		}
		if (!$appData = $this->registry->settings->get('apps/'.$app))
		{
			return false;
		}
        $language = $this->registry->settings->get('runtime/currentLanguage');
        if ($language == $this->registry->settings->get('config/defaultLanguage') || !in_array($language, $this->registry->settings->get('config/enabledLanguages', array()))) {
            $language = false;
        }

        if ($language) {
            if ($baseurl = $this->registry->settings->get('apps/'.$app.'/baseurlI18n', '')) {
                $baseurl = str_replace(':lang:', $language, $baseurl);
            } else {
                $baseurl = $this->registry->settings->get('apps/'.$app.'/baseurl');
            }
        } else {
            $baseurl = $this->registry->settings->get('apps/'.$app.'/baseurl');
        }

		$search = array('(',')');
		$replace = array('','');
        $regexSearch =  array();

        $allParameter = array_merge(isset($routeData['parameter']) ? $routeData['parameter'] : array(), $parameter);
		foreach ($allParameter as $param=>$value)
		{
            if (!$value || (isset($parameter[$param]) && !$parameter[$param]) || (isset($routeData['parameterPatterns'][$param]) && !isset($parameter[$param]) && !preg_match('#^'.$routeData['parameterPatterns'][$param].'$#', $value))) {
                $regexSearch[] = '#\([^:\)]*:'.$param.':[^\)]*\)#u';
            }
            $search[] = ':'.$param.':';
            $replace[] = urlencode($value);
		}
        $url = $routeData['route'];
        if (count($regexSearch)) {
            $url = preg_replace($regexSearch, '', $url);
        }
		$url = str_replace($search, $replace, $url);

        return $baseurl.$url;
	}

    public function link($title, $route, $parameter = array(), $attrs = '', $method = null, $app = null)
    {
        $url = $this->get($route, $parameter, $app);
        if (!$url) {
            return $title;
        }

        if (!$method) {
            if (isset($routeData['method'])) {
                $method = is_array($routeData['method']) ? array_shift($routeData['method']) : $routeData['method'];
            } else {
                $method = 'GET';
            }
        } elseif (isset($routeData['method']) && ((is_string($routeData['method']) && $routeData['method'] != $method) || (is_array($routeData['method']) && !in_array($method, $routeData['method'])))) {
            return false;
        }

        if ($method == 'GET') {
            return '<a href="'.htmlspecialchars($url).'"'.($attrs ? ' '.$attrs : '').'>'.$title.'</a>';
        } else {
            return '<form class="minimvcInlineForm" action="" method="POST">'.
                   (strtoupper($method) != 'POST' ? '<input type="hidden" name="REQUEST_METHOD" value="'.htmlspecialchars($method).'" />' : '').
                   '<button type="submit"'.($attrs ? ' '.$attrs : '').'>'.$title.'</button>'.
                   '</form>';
        }
    }
}