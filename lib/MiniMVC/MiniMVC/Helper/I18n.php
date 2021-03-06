<?php

class Helper_I18n extends MiniMVC_Helper
{
    protected static $cached = array();
    protected static $loaded = array();

    /**
     *
     * @param string $module
     * @param string $language the language to use (defaults to the current language)
     * @return MiniMVC_Translation
     */
    public function get($module = '_default', $language = null)
    {
        $language = $language ? $language : $this->registry->settings->get('currentLanguage');

        if (isset(self::$loaded[$language][$module])) {
            return self::$loaded[$language][$module];
        }

        if (!isset(self::$cached[$language])) {
            $cache = $this->registry->cache->get('i18n_'. $language);
            if ($cache !== null) {
                self::$cached[$language] = $cache;
            } else {
                $languages = array_merge(
                    (array) $this->registry->settings->get('config/fallbackLanguages/'.$language),
                    (array) $this->registry->settings->get('config/defaultLanguage')
                );
                self::$cached[$language] = $this->scanI18nFiles($language, $languages);
            }
        }

        

        

        

        $translationClass = $this->registry->settings->get('config/classes/translation', 'MiniMVC_Translation');
        self::$loaded[$language][$module] = new $translationClass((isset(self::$cached[$language][$module])
                                    ? self::$cached[$language][$module] : array()));
        return self::$loaded[$language][$module];
    }

    public function getLanguageChooserHtml($module = null, $partial = 'languageChooser')
    {
        $languages = array();
        $app = $this->registry->settings->get('currentApp');
        $defaultLanguage = $this->registry->settings->get('config/defaultLanguage');
        $currentLanguage = $this->registry->settings->get('currentLanguage');
        $enabledLanguages = $this->registry->settings->get('config/enabledLanguages', array());
        $route = $this->registry->settings->get('requestedRoute');
        $baseurl = $this->registry->settings->get('apps/'.$app.'/baseurl', '');
        $baseurlI18n = $this->registry->settings->get('apps/'.$app.'/baseurlI18n', $baseurl);

        sort($enabledLanguages);

        $i18n = $this->get('_languages', 'misc', 'misc');

        foreach ($enabledLanguages as $language) {
            if ($language == $defaultLanguage) {
                $url = $baseurl.$route;
            } else {
                $url = str_replace(':lang:', $language, $baseurlI18n).$route;
            }
            
            $languages[] = array('key' => $language, 'url' => $url, 'title' => $i18n[$language]);
        }
        $data = array('languages' => $languages, 'currentLanguage' => $currentLanguage);
        return $this->registry->helper->partial->get($partial, $data, $module ? $module : $this->module);
    }

    protected function scanI18nFiles($language, $languages)
    {
        $currentApp = $this->registry->settings->get('currentApp');
        $MiniMVC_i18n = array();

        $loaded = array();
        foreach (array_reverse(array_merge(array($language), $languages)) as $currentLanguage) {
            if (isset($loaded[$currentLanguage])) {
                continue;
            }
            $loaded[$currentLanguage] = true;
            if (is_file(MINIMVCPATH . 'data/i18n/' . $currentLanguage . '.php')) {
                include_once(MINIMVCPATH . 'data/i18n/' . $currentLanguage . '.php');
            }

            foreach ($this->registry->settings->get('modules') as $currentModule) {
                if (is_file(MODULEPATH . $currentModule . '/i18n/' . $currentLanguage . '.php')) {
                    include_once(MODULEPATH . $currentModule . '/i18n/' . $currentLanguage . '.php');
                }
            }

            if (is_file(DATAPATH . 'i18n/' . $currentLanguage . '.php')) {
                include_once(DATAPATH . 'i18n/' . $currentLanguage . '.php');
            }

            if ($currentApp) {
                if (is_file(APPPATH . $currentApp . '/i18n/' . $currentLanguage . '.php')) {
                    include_once(APPPATH . $currentApp . '/i18n/' . $currentLanguage . '.php');
                }
            }
        }

        $this->registry->cache->set('i18n_'. $language, $MiniMVC_i18n);
        return $MiniMVC_i18n;

//        self::$cached[$currentApp . '_' . $language . '_' . $fallbackLanguage] = $MiniMVC_i18n;
//
//        if ($this->registry->settings->get('useCache')) {
//            file_put_contents(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '_tmp.php', '<?php ' . "\n" . $this->registry->settings->varExport($MiniMVC_i18n, '$MiniMVC_i18n', 2));
//            rename(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '_tmp.php', CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.php');
//        }
    }

    public function fromArray($input, $default)
    {
        if (!is_array($input) && !is_object($input)) {
            return $default;
        }
        $language = $this->registry->settings->get('currentLanguage');

        $input = (array)$input;
        if (is_array($default)) {
            foreach ($default as $key => $value) {
                if (isset($input[$language][$key])) {
                    $default[$key] = $input[$language][$key];
                }
            }
            return $default;
        } else {
            if (isset($input[$language])) {
                return $input[$language];
            } else {
                return $default;
            }
        }
    }

    public function redirectToPreferredLanguage()
    {
        $currentLanguage = $this->registry->settings->get('currentLanguage');

        $preferredLanguage = $this->getPreferredLanguage();

        if ($preferredLanguage && in_array($preferredLanguage, $this->registry->settings->get('config/enabledLanguages', array()))) {
            if ($currentLanguage != $preferredLanguage) {
                $this->registry->settings->set('currentLanguage', $preferredLanguage);
                header('Location: ' . $this->registry->helper->url->get($this->registry->settings->get('currentRoute'), $this->registry->settings->get('currentRouteParameter', array())));
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
        $protocol = (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS'] || $_SERVER['HTTPS'] == 'off')
                    ? 'http' : 'https';
        $baseurl = $this->registry->settings->get('apps/' . $this->registry->settings->get('currentApp') . '/baseurl');
        $path = str_replace($protocol . '://' . $_SERVER['HTTP_HOST'], '', $baseurl);
        if ($language) {
            setcookie('minimvc_preferred_language', $language, time() + 2592000, $path, $_SERVER['HTTP_HOST'], false, false);
        } elseif (isset($_COOKIE['minimvc_preferred_language'])) {
            unset($_COOKIE['minimvc_preferred_language']);
            setcookie('minimvc_preferred_language', $language, time() - 2592000, $path, $_SERVER['HTTP_HOST'], false, false);
        }
        if ($redirect) {
            $this->redirectToPreferredLanguage();
        }
    }

}