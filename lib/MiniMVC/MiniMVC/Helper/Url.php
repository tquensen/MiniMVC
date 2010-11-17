<?php

class Helper_Url extends MiniMVC_Helper
{	
	public function get($route, $parameter = array(), $app = null)
	{
		$app = ($app) ? $app : $this->registry->settings->get('currentApp');
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
        $language = $this->registry->settings->get('currentLanguage');
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
        $anonymous = array();
        $regexSearch =  array();

        $url = $routeData['route'];

        $allParameter = array_merge(isset($routeData['parameter']) ? $routeData['parameter'] : array(), $parameter);
		foreach ($allParameter as $param=>$value)
		{
            if (!$value || (isset($parameter[$param]) && !$parameter[$param]) || (isset($routeData['parameterPatterns'][$param]) && !isset($parameter[$param]) && !preg_match('#^'.$routeData['parameterPatterns'][$param].'$#', $value))) {
                $regexSearch[] = '#\([^:\)]*:'.$param.':[^\)]*\)#u';
            }
            $currentSearch = ':'.$param.':';
            if (isset($parameter[$param]) && strpos($url, $currentSearch) === false) {
                $anonymous[] = urlencode($param) . '-' . urlencode($value);
            } else {
                $search[] = $currentSearch;
                $replace[] = urlencode($value);
            }
		}
        if (count($regexSearch)) {
            $url = preg_replace($regexSearch, '', $url);
        }
		$url = str_replace($search, $replace, $url);

        if (!empty($routeData['allowAnonymous']) && count($anonymous)) {
            if (substr($url, -1) == '/') {
                $url .= implode('/', $anonymous) . '/';
            } else {
                $url .= '/' . implode('/', $anonymous);
            }
        }

        return $baseurl.$url;
	}

    public function link($title, $route, $parameter = array(), $method = null, $attrs = '', $confirm = null, $postData = array(), $app = null)
    {
        $url = $this->get($route, $parameter, $app);
        if (!$url) {
            return $title;
        }

        try
		{
			$routeData = $this->registry->dispatcher->getRoute($route, $parameter, $app);
		}
		catch (Exception $e)
		{
			return $title;
		}

        if (!$method) {
            if (isset($routeData['method'])) {
                $method = is_array($routeData['method']) ? array_shift($routeData['method']) : $routeData['method'];
            } else {
                $method = 'GET';
            }
        } elseif (isset($routeData['method']) && ((is_string($routeData['method']) && strtoupper($routeData['method']) != strtoupper($method)) || (is_array($routeData['method']) && !in_array(strtoupper($method), array_map('strtoupper', $routeData['method']))))) {
            return false;
        }

        if ($method == 'GET') {
            return '<a href="'.htmlspecialchars($url).'"'.($attrs ? ' '.$attrs : '').($confirm ? ' onclick="return confirm(\''.htmlspecialchars($confirm).'\')' : '').'>'.$title.'</a>';
        } else {
            $return = '<form class="minimvcInlineForm" action="'.htmlspecialchars($url).'" method="POST"'.($confirm ? ' onsubmit="return confirm(\''.htmlspecialchars($confirm).'\')' : '').'>'.
                      (strtoupper($method) != 'POST' ? '<input type="hidden" name="REQUEST_METHOD" value="'.htmlspecialchars(strtoupper($method)).'" />' : '').
                      '<button type="submit"'.($attrs ? ' '.$attrs : '').'>'.$title.'</button>';
            foreach ((array) $postData as $postKey => $postValue) {
                $return .= '<input type="hidden" name="'.htmlspecialchars($postKey).'" value="'.htmlspecialchars($postValue).'" />';
            }
            $return.= '</form>';
        }
    }
}