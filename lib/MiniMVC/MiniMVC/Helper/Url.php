<?php

class Helper_Url extends MiniMVC_Helper
{
	public function get($route, $parameter = array(), $app = null, $language = null)
	{
		$app = ($app) ? $app : $this->registry->settings->get('currentApp');

        $language = $language ? $language : $this->registry->settings->get('currentLanguage');
        if ($language == $this->registry->settings->get('config/defaultLanguage') || !in_array($language, $this->registry->settings->get('config/enabledLanguages', array()))) {
            $language = false;
        }

        $baseurl = '';
        if ($language && $baseurl = $this->registry->settings->get('apps/'.$app.'/baseurlI18n', '')) {
            if (is_array($baseurl) && isset($baseurl[$language])) {
                $baseurl = $baseurl[$language];
            } else {
                $baseurl = str_replace(':lang:', $language, $baseurl);
            }
        }

        if (!$baseurl) {
            $baseurl = $this->registry->settings->get('apps/'.$app.'/baseurl');
        }

        if (substr($baseurl, 0, 1) == '/') {
            $baseurl = $this->registry->settings->get('currentHost', '') . $baseurl;
        }

        if ((!$route || $route == $this->registry->settings->get('config/defaultRoute')) && empty($parameter)) {
            return $baseurl;
        }

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


		$search = array('(',')');
		$replace = array('','');
        $anonymous = array();
        $regexSearch =  array();

        $url = $routeData['route'];

        $allParameter = array_merge(isset($routeData['parameter']) ? $routeData['parameter'] : array(), $parameter);
		foreach ($allParameter as $param=>$value)
		{
            //remove optional parameters if -it is set to false, -it is the default value or -it doesn't match the parameter pattern
            if (!$value || empty($parameter[$param]) || (isset($routeData['parameter'][$param]) && $value == $routeData['parameter'][$param]) || (isset($routeData['parameterPatterns'][$param]) && !preg_match('#^'.$routeData['parameterPatterns'][$param].'$#', $value))) {
                $regexSearch[] = '#\([^:\)]*:'.$param.':[^\)]*\)#U';
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
            $url = $this->get($route, $parameter, $app);
            if (!$url) {
                return $title;
            }
            return '<a href="'.htmlspecialchars($url).'"'.($attrs ? ' '.$attrs : '').($confirm ? ' onclick="return confirm(\''.htmlspecialchars($confirm).'\')"' : '').'>'.$title.'</a>';
        } else {
            $form = new MiniMVC_Form(array(
                'name' => md5($url).'Form',
                'route' => $route,
                'parameter' => $parameter,
                'method' => strtoupper($method),
                'class' => 'minimvcInlineForm'
            ));
            if ($confirm) {
                $form->setOption('attributes', array('onsubmit' => 'return confirm(\''.htmlspecialchars($confirm).'\')'));
            }
            $form->setElement(new MiniMVC_Form_Element_Button('_submit', array('label' => $title, 'attributes' => $attrs ? $attrs : array())));
            foreach ((array) $postData as $postKey => $postValue) {
                $form->setElement(new MiniMVC_Form_Element_Hidden($postKey, array('alwaysDisplayDefault' => true, 'defaultValue' => $postValue)));
            }
            return $this->registry->helper->partial->get('form', array('form' => $form));
        }
    }

    public function userCanCall($route, $params = array(), $app = null)
    {
        try {
            $routeData = $this->registry->dispatcher->getRoute($route, $params, $app);
        } catch (Exception $e) {
            return false;
        }

        if (isset($routeData['rights']) && $routeData['rights'] && !$this->registry->guard->userHasRight($routeData['rights'])) {
            return false;
        }

        return true;
    }
}