<?php 
class MiniMVC_Controller
{
	protected $view = null;
	protected $registry = null;
	
	public function __construct($view)
	{
		$this->view = $view; 
		$this->registry = MiniMVC_Registry::getInstance();

        $this->construct();
	}

    protected function construct()
    {
        
    }

    protected function redirect($route, $params = array(), $app = null)
    {
        $url = $this->registry->helper->Url->get($route, $params, $app);
        if ($url)
        {
            header('Location: '.$url);
            $this->registry->template->setLayout(false);
            return null;
        }
    }

	protected function delegate($route, $params = array())
	{
		return $this->registry->dispatcher->callRoute($route, $params);
	}
	
	protected function delegate401()
	{
		if (isset($this->registry->settings->config['error401Route']))
		{
			return $this->delegate($this->registry->settings->config['error401Route']);
		}
		return false;
	}
	
	protected function delegate403()
	{
		if (isset($this->registry->settings->config['error403Route']))
		{
			return $this->delegate($this->registry->settings->config['error403Route']);
		}
		return false;
	}
	
	protected function delegate404()
	{
		if (isset($this->registry->settings->config['error404Route']))
		{
			return $this->delegate($this->registry->settings->config['error404Route']);
		}
		return false;
	}
	

}