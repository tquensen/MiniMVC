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
    protected $content = null;
	protected $helper = null;
    protected $t = null;

    /**
     *
     * @param mixed $module the name of the associated module or null
     */
	public function __construct($module = null, $defaultFile = null)
	{
        $this->module = $module;
        $this->file = $defaultFile;
		$this->registry = MiniMVC_Registry::getInstance();

        $this->helper = $this->registry->helper;
        $this->t = $this->helper->i18n->get($this->module);
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
        if ($var == 'helper')
        {
            return $this->helper;
        }
        elseif($var == 't')
        {
            return $this->t;
        }
		return (isset($this->vars[$var])) ? $this->vars[$var] : '';
	}

    public function  __toString()
    {
        if ($this->file === null) {
            return (string) $this->content;
        }

        $file = $this->file;
        
		$app = $this->registry->settings->get('runtime/currentApp');

        $format = $this->registry->template->getFormat();
        $formatString = ($format) ? '.'.$format : '';

        if ($this->module != '_default')
        {
            $appPath = APPPATH.$app.'/view/'.$module.'/'.$file.$formatString.'.php';
            $path = MODULEPATH.$module.'/view/'.$file.$formatString.'.php';
        }
        else
        {
            $appPath = false;
            $path = APPPATH.$app.'/view/'.$file.$formatString.'.php';
        }

        if ($appPath && is_file($appPath))
		{
			$path = $appPath;
		}
		elseif (!is_file($path))
		{
			throw new Exception('View "'.$path.'" not found!');
		}

		extract($this->vars);
        $helper = $this->helper;
        $t = $this->t;
		ob_start();
		include ($path);
		return ob_get_clean();
    }

    public function setFile($file = null, $module = null) {
        $this->file = $file;
        if ($module !== null) {
            $this->module = $module;
        }
    }

    public function setModule($module = '_default') {
        $this->module = $module;
    }

    /**
     *
     * @param string $file the file to parse
     * @param mixed $module the name of the module that contains the file or null to use the current module
     * @return MiniMVC_View returns this view class
     */
	public function parse($file = null, $module = null)
	{
        $this->setFile($file, $module);
        return $this;
	}

    public function parseText($text = '')
    {
        $this->file = null;
        $this->content = $text;

        return $this;
    }
	
	public function parseJSON($data = null)
	{
        $this->file = null;
        $this->content = json_encode(($data === null) ? $this->vars : $data);

        return $this;
	}
	
	public function parseXML($data)
	{
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('root');
				
		$this->writeXML($xml, $data);
		
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