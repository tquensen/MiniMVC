<?php
class MiniMVC_Core
{
	protected static $basepath = null;
	
	protected $settings = null;
	protected $dispatcher = null;
	protected $helper = null;
	protected $template = null;
	protected $guard = null;
	protected $app = null;
	protected $environment = null;
	protected $currentLanguage = null;
	
	public function __construct($app = null, $language = null, $environment = null, $settings = null)
	{		
		$this->environment = ($environment) ? $environment : 'prod';
		$settings = ($settings) ? $settings : new MiniMVC_Settings($app, $this->environment);	
		
		if ($app && isset($settings->apps[$app]))
		{
			$this->app = $app;
		}
		else
		{
			$this->app = (isset($settings->config['defaultApp'])) ? $settings->config['defaultApp'] : 'Frontend';
		}
		$settings->app = $this->app;
		$settings->environment = $this->environment;
		
		$registry = MiniMVC_Registry::getInstance();
		
		$registry->core = $this;
		$registry->settings = $settings;
				
		/*
		$helpers = (isset($this->settings->classes['helper'])) ? $this->settings->classes['helper'] : 'MiniMVC_Helper';
		$registry->helper = new $helpers();
		*/
		if ($language && in_array($language, (array) $registry->settings->config['enabledLanguages']))
		{
			$this->currentLanguage = $language;
		}
		elseif (isset($registry->settings->config['defaultLanguage']))
		{
			$this->currentLanguage = $registry->settings->config['defaultLanguage'];
		}
		else
		{
			$this->currentLanguage = (isset($registry->settings->config['enabledLanguages'])) ? $registry->settings->config['enabledLanguages'][0] : 'en';
		}
		
		$registry->db->connect();
	}
	
	public function __get($var)
	{
		return (property_exists($this, $var)) ? $this->$var : null;
	}
	
	public function setTemplate($template)
	{
		$this->template = $template;
	}
	
	public function setCurrentLanguage($language)
	{
		$registry = MiniMVC_Registry::getInstance();
		if (in_array($language, (array) $registry->settings->config['enabledLanguages']))
		{
			$this->currentLanguage = $language;
		}
	}
	
	public function parse($route)
	{
		$registry = MiniMVC_Registry::getInstance();
		$mainContent = $registry->dispatcher->dispatch($route);
		$registry->template->addToSlot('main', $mainContent);
		return $registry->template->parse($this->app);
	}
		
	public static function autoload($class)
	{
		$class = str_replace('_', '/', $class);
		if (file_exists(BASEPATH.$class.'.php'))
		{
			include_once (BASEPATH.$class.'.php');
			return;
		}
		
		$registry = MiniMVC_Registry::getInstance();
		if (isset($registry->settings->config['autoloadPaths']))
		{
			foreach ($registry->settings->config['autoloadPaths'] as $path)
			{
				if (file_exists(BASEPATH.$path.'/'.$class.'.php'))
				{
					include_once (BASEPATH.$path.'/'.$class.'.php');
					return;
				}
			}
		}	
	}
}