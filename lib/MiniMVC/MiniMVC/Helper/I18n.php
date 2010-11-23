<?php

class Helper_I18n extends MiniMVC_Helper
{
    protected static $cached = array();
    protected static $loaded = array();

    /**
     *
     * @param string $module
     * @param string $language the language to use (defaults to the current language)
     * @param string $fallbackLanguage the fallback language (defaults to the default language)
     * @return MiniMVC_Translation
     */
    public function get($module = '_default', $language = null, $fallbackLanguage = null, $app = null)
    {
        $language = $language ? $language : $this->registry->settings->get('currentLanguage');
        $fallbackLanguage = $fallbackLanguage ? $fallbackLanguage : $this->registry->settings->get('config/defaultLanguage');
        $currentApp = $app ? $app : $this->registry->settings->get('currentApp');
        
        if (isset(self::$loaded[$currentApp . '_' . $language . '_' . $fallbackLanguage][$module])) {
            return self::$loaded[$currentApp . '_' . $language . '_' . $fallbackLanguage][$module];
        }

        if (empty(self::$cached[$currentApp . '_' . $language . '_' . $fallbackLanguage])) {
            if ($this->registry->settings->get('useCache')) {
                if (file_exists(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.php')) {
                    include_once(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.php');
                    if (isset($MiniMVC_i18n)) {
                        self::$cached[$currentApp . '_' . $language . '_' . $fallbackLanguage] = $MiniMVC_i18n;
                    }
                } elseif (file_exists(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.lock')) {
                    for ($i = 0; $i < 10; $i++) {
                        usleep(50000);
                        if (!file_exists(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.lock')) {
                            continue;
                        }
                    }

                    if (file_exists(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.php')) {
                        include_once(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.php');
                        if (isset($MiniMVC_i18n)) {
                            self::$cached[$currentApp . '_' . $language . '_' . $fallbackLanguage] = $MiniMVC_i18n;
                        }
                    } else {
                        file_put_contents(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.lock', 'locked');
                        $this->scanI18nFiles($currentApp, $language, $fallbackLanguage);
                        unlink(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.lock');
                    }
                } else {
                    file_put_contents(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.lock', 'locked');
                    $this->scanI18nFiles($currentApp, $language, $fallbackLanguage);
                    unlink(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.lock');
                }
            } else {
                $this->scanI18nFiles($currentApp, $language, $fallbackLanguage);
            }
        }

        $translationClass = $this->registry->settings->get('config/classes/translation', 'MiniMVC_Translation');
        self::$loaded[$currentApp . '_' . $language . '_' . $fallbackLanguage][$module] = new $translationClass((isset(self::$cached[$currentApp . '_' . $language . '_' . $fallbackLanguage][$module])
                                    ? self::$cached[$currentApp . '_' . $language . '_' . $fallbackLanguage][$module] : array()));
        return self::$loaded[$currentApp . '_' . $language . '_' . $fallbackLanguage][$module];
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

    protected function scanI18nFiles($currentApp, $language, $fallbackLanguage)
    {
        $MiniMVC_i18n = array();

        //load fallback language first (if it differs from current language)
        if ($language != $fallbackLanguage) {
            if (is_file(MINIMVCPATH . 'data/i18n/' . $fallbackLanguage . '.php')) {
                include_once(MINIMVCPATH . 'data/i18n/' . $fallbackLanguage . '.php');
            }

            if (is_file(DATAPATH . 'i18n/' . $fallbackLanguage . '.php')) {
                include_once(DATAPATH . 'i18n/' . $fallbackLanguage . '.php');
            }

            foreach ($this->registry->settings->get('modules') as $currentModule) {
                if (is_file(MODULEPATH . $currentModule . '/i18n/' . $fallbackLanguage . '.php')) {
                    include_once(MODULEPATH . $currentModule . '/i18n/' . $fallbackLanguage . '.php');
                }
            }

            if ($currentApp) {
                if (is_file(APPPATH . $currentApp . '/i18n/' . $fallbackLanguage . '.php')) {
                    include_once(APPPATH . $currentApp . '/i18n/' . $fallbackLanguage . '.php');
                }
            }
        }

        //overwrite fallback with current language where available
        if (is_file(MINIMVCPATH . 'data/i18n/' . $language . '.php')) {
            include_once(MINIMVCPATH . 'data/i18n/' . $language . '.php');
        }

        if (is_file(DATAPATH . 'i18n/' . $language . '.php')) {
            include_once(DATAPATH . 'i18n/' . $language . '.php');
        }

        foreach ($this->registry->settings->get('modules') as $currentModule) {
            if (is_file(MODULEPATH . $currentModule . '/i18n/' . $language . '.php')) {
                include_once(MODULEPATH . $currentModule . '/i18n/' . $language . '.php');
            }
        }

        if ($currentApp) {
            if (is_file(APPPATH . $currentApp . '/i18n/' . $language . '.php')) {
                include_once(APPPATH . $currentApp . '/i18n/' . $language . '.php');
            }
        }

        self::$cached[$currentApp . '_' . $language . '_' . $fallbackLanguage] = $MiniMVC_i18n;

        if ($this->registry->settings->get('useCache')) {
            file_put_contents(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '_tmp.php', '<?php ' . "\n" . $this->registry->settings->varExport($MiniMVC_i18n, '$MiniMVC_i18n', 2));
            rename(CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '_tmp.php', CACHEPATH . 'i18n_' . $currentApp . '_' . $language . '_' . $fallbackLanguage . '.php');
        }
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
                $this->registry->settings->set('runtime/currentLanguage', $preferredLanguage);
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