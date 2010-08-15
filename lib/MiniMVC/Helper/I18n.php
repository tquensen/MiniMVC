<?php

class Helper_I18n extends MiniMVC_Helper
{
	protected static $cached = array();
	protected static $loaded = array();
	
	public function __construct()
	{
		$this->registry = MiniMVC_Registry::getInstance();
	}
	
	public function get($module = '_default')
	{
		if (isset(self::$loaded[$module]))
		{
			return self::$loaded[$module];
		}

        if (!self::$cached)
        {
            $language = $this->registry->settings->get('runtime/currentLanguage');
            $currentApp = $this->registry->settings->get('runtime/currentApp');
            if ($this->registry->settings->get('runtime/useCache') && is_file(CACHEPATH.'i18n_'.$currentApp.'_'.$language.'.php'))
            {
                include_once(CACHEPATH.'i18n_'.$currentApp.'_'.$language.'.php');
                if (isset($MiniMVC_i18n))
                {
                    self::$cached = $MiniMVC_i18n;
                }
            }

            if (!self::$cached)
            {
                $MiniMVC_i18n = array();
                if (is_file(DATAPATH.'i18n/'.$language.'.php'))
                {
                    include_once(DATAPATH.'i18n/'.$language.'.php');
                }

                foreach ($this->registry->settings->get('modules') as $currentModule)
                {
                    if (is_file(MODULEPATH.$currentModule.'/i18n/'.$language.'.php'))
                    {
                        include_once(MODULEPATH.$currentModule.'/i18n/'.$language.'.php');
                    }
                }

                if ($currentApp)
                {
                    if (is_file(APPPATH.$currentApp.'/i18n/'.$language.'.php'))
                    {
                        include_once(APPPATH.$currentApp.'/i18n/'.$language.'.php');
                    }
                }

                self::$cached = $MiniMVC_i18n;

                if ($this->registry->settings->get('runtime/useCache')) {
                    file_put_contents(CACHEPATH.'i18n_'.$currentApp.'_'.$language.'.php', '<?php ' . "\n" . $this->registry->settings->varExport($MiniMVC_i18n, '$MiniMVC_i18n', 2));
                }

            }
        }

		$translationClass = $this->registry->settings->get('config/classes/translation', 'MiniMVC_Translation');
		self::$loaded[$module] = new $translationClass((isset(self::$cached[$module]) ? self::$cached[$module] : array()));
		return self::$loaded[$module];
	}

	public function fromArray($input, $default)
	{
		if (!is_array($input) && !is_object($input))
		{
			return $default;
		}
		$language = $this->registry->settings->get('runtime/currentLanguage');

		$input = (array) $input;
		if (is_array($default))
		{
			foreach ($default as $key=>$value)
			{
				if (isset($input[$language][$key]))
				{
					$default[$key] = $input[$language][$key];
				}
			}
			return $default;
		}
		else
		{
			if (isset($input[$language]))
			{
				return $input[$language];
			}
			else
			{
				return $default;
			}
		}
	}

    public function redirectToPreferredLanguage()
	{
        $currentLanguage = $this->registry->settings->get('runtime/currentLanguage');

        $preferredLanguage = $this->getPreferredLanguage();

        if ($preferredLanguage && in_array($preferredLanguage, $this->registry->settings->get('config/enabledLanguages', array()))) {
            if ($currentLanguage != $preferredLanguage) {
                $this->registry->settings->set('runtime/currentLanguage', $preferredLanguage);
                header('Location: ' . $this->registry->helper->Url->get($this->registry->settings->get('runtime/currentRoute'), $this->registry->settings->get('runtime/currentRouteParameter')));
                exit;
            }
        }

	}

    public function getPreferredLanguage()
    {
        if (isset($_COOKIE['minimvc_preferred_language'])) {
            return $_COOKIE['minimvc_preferred_language'];
        }
        return false;
    }

    public function setPreferredLanguage($language = null, $redirect = false)
    {
        $protocol = (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS'] || $_SERVER['HTTPS'] == 'off') ? 'http' : 'https';
        $baseurl = $this->registry->settings->get('apps/'.$this->registry->settings->get('runtime/currentApp').'/baseurl');
        $path = str_replace($protocol . '://' . $_SERVER['HTTP_HOST'], '', $baseurl);
        if ($language) {    
            setcookie('minimvc_preferred_language', $language, time() + 2592000, $path, $_SERVER['HTTP_HOST'], false, false);
        } elseif(isset($_COOKIE['minimvc_preferred_language'])) {
            unset($_COOKIE['minimvc_preferred_language']);
            setcookie('minimvc_preferred_language', $language, time() - 2592000, $path, $_SERVER['HTTP_HOST'], false, false);
        }
        if ($redirect) {
            $this->redirectToPreferredLanguage();
        }
    }
}