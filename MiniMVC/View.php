<?php 
class MiniMVC_View
{
	protected $vars = array();
	protected $registry = null;
    protected $module = null;
	protected $helper = null;
    protected $t = null;
    
	public function __construct($module = null)
	{
        $this->module = $module;
		$this->registry = MiniMVC_Registry::getInstance();

        $this->helper = $this->registry->helper;
        $this->t = $this->helper->I18n->get($this->module);
	}

    public function getModule()
    {
        return $this->module;
    }

    protected function getSlot($slot, $array = false, $glue = '')
    {
        if ($this->module != '_default') {
            return '';
        }
        return $this->registry->template->getSlot($slot, $array, $glue);
    }

	public function __set($var, $value)
	{
		$this->vars[$var] = $value;
	}
	
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
	
	public function parse($file, $module = null)
	{
        if ($module === null)
        {
            if ($this->module === null)
            {
                return false;
            }
            $module = $this->module;
        }
		$app = $this->registry->settings->currentApp;
		
        $format = $this->registry->template->getFormat();
        $formatString = ($format) ? '.'.$format : '';

        if ($module != '_default')
        {
            $appPath = 'App/'.$app.'/View/'.$module.'/'.$file.$formatString.'.php';
            $path = 'Module/'.$module.'/View/'.$file.$formatString.'.php';
        }
        else
        {
            $appPath = false;
            $path = 'App/'.$app.'/View/'.$file.$formatString.'.php';
        }
        
        if ($appPath && is_file(BASEPATH.$appPath))
		{
			$path = $appPath;
		}
		elseif (!is_file(BASEPATH.$path))
		{
			throw new Exception('View "'.$path.'" not found!');
		}
		extract($this->vars);
        $helper = $this->helper;
        $t = $this->t;
		ob_start();
		include (BASEPATH.$path);
		return ob_get_clean();
	}
	
	public function toJSON($data = null)
	{
		return json_encode(($data !== null) ? $this->vars : $data);
	}
	
	public function toXML($data)
	{
		$xml = new XmlWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('root');
				
		$this->writeXML($xml, $data);
		
		$xml->endElement();
		echo $xml->outputMemory(true);
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