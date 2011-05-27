<?php
/**
 * MiniMVC_View is the default view class
 *
 * @property MiniMVC_Helpers $helper
 * @property MiniMVC_Translation $t
 */
class MiniMVC_View
{
	protected $vars = array();
	protected $registry = null;
    protected $module = null;
    protected $file = null;
    protected $app = null;
    protected $content = null;
	protected $helper = null;
    protected $t = null;
    protected $cache = null;
    protected $cacheContent = null;

    /**
     *
     * @param mixed $module the name of the associated module or null
     * @param mixed $defaultFile the default file to use
     */
	public function __construct($module = null, $defaultFile = null, $app = null)
	{
		$this->registry = MiniMVC_Registry::getInstance();

        $this->module = $module;
        $this->file = $defaultFile;
        $this->app = $app ? $app : $this->registry->settings->get('currentApp');

        $this->helper = $this->registry->helper;
        if ($module) {
            $this->t = $this->helper->i18n->get($this->module);
        }
	}

    /**
     *
     * @return mixed returns the name of the associated module or null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     *
     * @param string $var the name of the var to set
     * @param mixed $value the value to store
     */
	public function __set($var, $value)
	{
		$this->vars[$var] = $value;
	}

    /**
     *
     * @param string $var the name of the var
     * @return mixed the stored value
     */
	public function __get($var)
	{
        if ($var == 'helper' || $var == 'h')
        {
            return $this->helper;
        }
        elseif($var == 't')
        {
            return $this->t;
        }
        elseif($var == 'o')
        {
            return $this->helper->text;
        }
		return (isset($this->vars[$var])) ? $this->vars[$var] : '';
	}

    public function parse()
    {
        if ($this->cacheContent) {
            return $this->cacheContent;
        }

        if ($this->file === null) {
            $return = (string) $this->content;
        } else {

            $_file = $this->file;

            $_app = $this->app;
            $_theme = $this->registry->layout->getTheme();

            $_format = $this->registry->layout->getFormat();
            $_formatString = ($_format) ? '.'.$_format : '';

            $_path = null;
            
            if ($_cache = $this->registry->cache->get('viewCached/'.$_app.'_'.$_theme.'_'.$this->module.'_'.str_replace('/', '__', $_file.$_formatString))) {
                $_path = $_cache;
            } else {
                if ($this->module != '_default')
                {
                    if ($_theme && is_file(APPPATH.$_app.'/view/'.$_theme.'/'.$this->module.'/'.$_file.$_formatString.'.php')) {
                        $_path = APPPATH.$_app.'/view/'.$_theme.'/'.$this->module.'/'.$_file.$_formatString.'.php';
                    } elseif ($_theme && is_file(VIEWPATH.$_theme.'/'.$this->module.'/'.$_file.$_formatString.'.php')) {
                        $_path = VIEWPATH.$_theme.'/'.$this->module.'/'.$_file.$_formatString.'.php';
                    } elseif ($_theme && is_file(THEMEPATH.$_theme.'/view/'.$this->module.'/'.$_file.$_formatString.'.php')) {
                        $_path = THEMEPATH.$_theme.'/view/'.$this->module.'/'.$_file.$_formatString.'.php';
                    } elseif (is_file(APPPATH.$_app.'/view/'.$this->module.'/'.$_file.$_formatString.'.php')) {
                        $_path = APPPATH.$_app.'/view/'.$this->module.'/'.$_file.$_formatString.'.php';
                    } elseif (is_file(VIEWPATH.$this->module.'/'.$_file.$_formatString.'.php')) {
                        $_path = VIEWPATH.$this->module.'/'.$_file.$_formatString.'.php';
                    } elseif(is_file(MODULEPATH.$this->module.'/view/'.$_file.$_formatString.'.php')) {
                        $_path = MODULEPATH.$this->module.'/view/'.$_file.$_formatString.'.php';
                    }
                    if (!$_path && !$_format) {
                        $_defaultFormat = $this->registry->settings->get('config/defaultFormat');
                        if ($_theme && is_file(APPPATH.$_app.'/view/'.$_theme.'/'.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php')) {
                            $_path = APPPATH.$_app.'/view/'.$_theme.'/'.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php';
                        } elseif ($_theme && is_file(VIEWPATH.$_theme.'/'.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php')) {
                            $_path = VIEWPATH.$_theme.'/'.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php';
                        } elseif ($_theme && is_file(THEMEPATH.$_theme.'/view/'.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php')) {
                            $_path = THEMEPATH.$_theme.'/view/'.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php';
                        } elseif (is_file(APPPATH.$_app.'/view/'.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php')) {
                            $_path = APPPATH.$_app.'/view/'.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php';
                        } elseif (is_file(VIEWPATH.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php')) {
                            $_path = VIEWPATH.$this->module.'/'.$_file.'.'.$_defaultFormat.'.php';
                        } elseif(is_file(MODULEPATH.$this->module.'/view/'.$_file.'.'.$_defaultFormat.'.php')) {
                            $_path = MODULEPATH.$this->module.'/view/'.$_file.'.'.$_defaultFormat.'.php';
                        }
                    }
                }
                else
                {
                    if ($_theme && is_file(APPPATH.$_app.'/view/'.$_theme.'/'.$_file.$_formatString.'.php')) {
                        $_path = APPPATH.$_app.'/view/'.$_theme.'/'.$_file.$_formatString.'.php';
                    } elseif ($_theme && is_file(VIEWPATH.$_theme.'/'.$_file.$_formatString.'.php')) {
                        $_path = VIEWPATH.$_theme.'/'.$_file.$_formatString.'.php';
                    } elseif ($_theme && is_file(THEMEPATH.$_theme.'/view/'.$_file.$_formatString.'.php')) {
                        $_path = THEMEPATH.$_theme.'/view/'.$_file.$_formatString.'.php';
                    } elseif (is_file(APPPATH.$_app.'/view/'.$_file.$_formatString.'.php')) {
                        $_path = APPPATH.$_app.'/view/'.$_file.$_formatString.'.php';
                    } elseif (is_file(VIEWPATH.$_file.$_formatString.'.php')) {
                        $_path = VIEWPATH.$_file.$_formatString.'.php';
                    }
                    if (!$_path) {
                        if (!$_format) {
                            $_defaultFormat = $this->registry->settings->get('config/defaultFormat');
                            if ($_theme && is_file(APPPATH.$_app.'/view/'.$_theme.'/'.$_file.'.'.$_defaultFormat.'.php')) {
                                $_path = APPPATH.$_app.'/view/'.$_theme.'/'.$_file.'.'.$_defaultFormat.'.php';
                            } elseif ($_theme && is_file(VIEWPATH.$_theme.'/'.$_file.'.'.$_defaultFormat.'.php')) {
                                $_path = VIEWPATH.$_theme.'/'.$_file.'.'.$_defaultFormat.'.php';
                            } elseif ($_theme && is_file(THEMEPATH.$_theme.'/view/'.$_file.'.'.$_defaultFormat.'.php')) {
                                $_path = THEMEPATH.$_theme.'/view/'.$_file.'.'.$_defaultFormat.'.php';
                            } elseif (is_file(APPPATH.$_app.'/view/'.$_file.'.'.$_defaultFormat.'.php')) {
                                $_path = APPPATH.$_app.'/view/'.$_file.'.'.$_defaultFormat.'.php';
                            } elseif (is_file(VIEWPATH.$_file.'.'.$_defaultFormat.'.php')) {
                                $_path = VIEWPATH.$_file.'.'.$_defaultFormat.'.php';
                            }
                        } else {
                            $_defaultLayout = $this->registry->settings->get('config/defaultLayout', 'default');
                            if ($_defaultLayout != $_file) {
                                if ($_theme && is_file(APPPATH.$_app.'/view/'.$_theme.'/'.$_defaultLayout.$_formatString.'.php')) {
                                    $_path = APPPATH.$_app.'/view/'.$_theme.'/'.$_defaultLayout.$_formatString.'.php';
                                } elseif ($_theme && is_file(VIEWPATH.$_theme.'/'.$_defaultLayout.$_formatString.'.php')) {
                                    $_path = VIEWPATH.$_theme.'/'.$_defaultLayout.$_formatString.'.php';
                                } elseif ($_theme && is_file(THEMEPATH.$_theme.'/'.$_defaultLayout.$_formatString.'.php')) {
                                    $_path = THEMEPATH.$_theme.'/'.$_defaultLayout.$_formatString.'.php';
                                } elseif (is_file(APPPATH.$_app.'/view/'.$_defaultLayout.$_formatString.'.php')) {
                                    $_path = APPPATH.$_app.'/view/'.$_defaultLayout.$_formatString.'.php';
                                } elseif (is_file(VIEWPATH.$_defaultLayout.$_formatString.'.php')) {
                                    $_path = VIEWPATH.$_defaultLayout.$_formatString.'.php';
                                }
                            }
                        }
                    }
                }
                if (!$_path)
                {
                    throw new Exception('View "'.$_file.$_formatString.'" for module '.$this->module.' not found!', 404);
                }
                $this->registry->cache->set('viewCached/'.$_app.'_'.$_theme.'_'.$this->module.'_'.str_replace('/', '__', $_file.$_formatString), $_path);
            }



            extract($this->vars);
            $h = $this->helper;
            $t = $this->t;
            $o = $this->helper->text;
            try {
                ob_start();
                include ($_path);
                $return = ob_get_clean();
            } catch (Exception $e) {
                ob_end_clean();
                throw $e;
            }
        }

        if ($this->cache) {
            $this->cache->save($return);
        }
        return $return;
    }

    public function setFile($file = null, $module = null) {
        $this->file = $file;
        if ($module !== null) {
            $this->module = $module;
        }
        return $this;
    }

    public function setModule($module = '_default') {
        $this->module = $module;
        return $this;
    }

    public function setApp($app = null) {
        $this->app = $app ? $app : $this->registry->settings->get('currentApp');
        return $this;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function setCache($cache)
    {
        $this->cache = $cache;
        return $this->cache->check();
    }

    public function selectCache($dependencies = array(), $tokens = array(), $bindToUrl = true, $ttl = 0)
    {
        $this->cache = $this->registry->helper->cache->get(
            $dependencies, $tokens, $bindToUrl
        );
        if ($ttl) {
            $this->cache->setTTL($ttl);
        }
        return $this->checkCache();
    }

    public function checkCache()
    {
        if (!$this->cache) {
            return false;
        }
        return $this->cache->check();
    }

    public function deleteCache($tokens = array())
    {
        $this->registry->helper->cache->delete($tokens);
    }

    public function prepareCache()
    {
        if (!$this->cache || !$content = $this->cache->load()) {
            throw new Exception('Cache for View "'.$_file.$_formatString.'" for module '.$this->module.' not found!', 404);
        }
        $this->cacheContent = $content;
        return $this;
    }

    /**
     *
     * @param string $file the file to use
     * @param mixed $module the name of the module that contains the file or null to use the current module
     * @return MiniMVC_View returns this view class
     */
	public function prepare($file = null, $module = null)
	{
        $this->setFile($file, $module);
        return $this;
	}

    public function prepareEmpty()
    {
        $this->file = null;
        $this->content = '';

        return $this;
    }

    public function prepareText($text = '')
    {
        $this->file = null;
        $this->content = $text;

        return $this;
    }
	
	public function prepareJSON($data = null)
	{
        $this->file = null;
        $this->content = json_encode(($data === null) ? $this->vars : $data);

        return $this;
	}
	
	public function prepareXML($data = null)
	{
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('root');
				
		$this->writeXML($xml, ($data === null) ? $this->vars : $data);
		
		$xml->endElement();

        $this->file = null;
		$this->content = $xml->outputMemory(true);

        return $this;
	}
	
	private function writeXML(XMLWriter $xml, $data){
	    foreach($data as $key => $value){
	        if(is_array($value)){
	            $xml->startElement($key);
	            $this->writeXML($xml, $value);
	            $xml->endElement();
	            continue;
	        }
	        $xml->writeElement($key, $value);
	    }
	}
}