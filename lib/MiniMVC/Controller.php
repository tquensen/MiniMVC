<?php
/**
 * MiniMVC_Controller is the base class for all controller classes
 */
class MiniMVC_Controller
{
    /**
     *
     * @var MiniMVC_View
     */
	protected $view = null;
    /**
     *
     * @var MiniMVC_Registry
     */
	protected $registry = null;

    /**
     *
     * @param MiniMVC_View $view the view for the controller
     */
	public function __construct($view)
	{
		$this->view = $view; 
		$this->registry = MiniMVC_Registry::getInstance();

        $this->construct();
	}

    /**
     * Constructor function which is called on construct
     */
    protected function construct()
    {
        
    }

    /**
     *
     * @param string $route an internal route name
     * @param array $params parameter for the route
     * @param mixed $app the name of the app or null for current app
     * @return null
     */
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

    /**
     *
     * @param string $route an internal route name
     * @param array $params parameter for the route
     * @return string returns the parsed output of the delegated action
     */
	protected function delegate($route, $params = array())
	{
		return $this->registry->dispatcher->callRoute($route, $params);
	}

    /**
     *
     * @return string return the output of the configured 401 action
     */
	protected function delegate401()
	{
		if ($route = $this->registry->settings->get('config/error401Route'))
		{
			return $this->delegate($route);
		}
		return false;
	}

    /**
     *
     * @return string return the output of the configured 403 action
     */
	protected function delegate403()
	{
		if ($route = $this->registry->settings->get('config/error403Route'))
		{
			return $this->delegate($route);
		}
		return false;
	}

    /**
     *
     * @return string return the output of the configured 404 action
     */
	protected function delegate404()
	{
		if ($route = $this->registry->settings->get('config/error404Route'))
		{
			return $this->delegate($route);
		}
		return false;
	}
	
    /**
     *
     * @return string return the output of the configured 404 action
     */
	protected function delegate500()
	{
		if ($route = $this->registry->settings->get('config/error500Route'))
		{
			return $this->delegate($route);
		}
		return false;
	}
}