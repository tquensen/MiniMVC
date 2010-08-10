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
            if ($this->registry->settings->get('runtime/useCache') && is_file(BASEPATH.'Cache/I18n_'.$currentApp.'_'.$language.'.php'))
            {
                include_once(BASEPATH.'Cache/I18n_'.$currentApp.'_'.$language.'.php');
                if (isset($MiniMVC_i18n))
                {
                    self::$cached = $MiniMVC_i18n;
                }
            }

            if (!self::$cached)
            {
                $MiniMVC_i18n = array();
                if (is_file(BASEPATH.'I18n/'.$language.'.php'))
                {
                    include_once(BASEPATH.'I18n/'.$language.'.php');
                }

                foreach ($this->registry->settings->get('modules') as $currentModule)
                {
                    if (is_file(MODULEPATH.$currentModule.'/I18n/'.$language.'.php'))
                    {
                        include_once(MODULEPATH.$currentModule.'/I18n/'.$language.'.php');
                    }
                }

                if ($currentApp)
                {
                    if (is_file(APPPATH.$currentApp.'/I18n/'.$language.'.php'))
                    {
                        include_once(APPPATH.$currentApp.'/I18n/'.$language.'.php');
                    }
                }

                self::$cached = $MiniMVC_i18n;

                if ($this->registry->settings->get('runtime/useCache')) {
                    file_put_contents(BASEPATH.'Cache/I18n_'.$currentApp.'_'.$language.'.php', '<?php $MiniMVC_i18n = '.var_export($MiniMVC_i18n, true).';');
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
}