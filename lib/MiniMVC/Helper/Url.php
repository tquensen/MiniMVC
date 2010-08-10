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
		$baseurl = (isset($appData['baseurl'])) ? $appData['baseurl'] : '/';
		$search = array();
		$replace = array();
		foreach ($routeData['parameter'] as $param=>$value)
		{
			$search[] = ':'.$param.':';
			$replace[] = urldecode($value);
		}
		return $baseurl.str_replace($search, $replace, $routeData['route']);
	}
}