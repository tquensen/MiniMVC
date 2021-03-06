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
     *
     * @return MiniMVC_View
     */
    public function getView()
    {
        return $this->view;
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
    protected function redirect($route, $params = array(), $code = 302, $app = null)
    {
        $url = $this->registry->helper->url->get($route, $params, $app);
        if ($url)
        {
            header('Location: '.$url, null, $code);

            $this->registry->layout->setLayout(false);
        }
        return $this->view->prepareEmpty();
    }

    /**
     *
     * @param string $route an internal route name
     * @param array $params parameter for the route
     * @return MiniMVC_View returns the prepared view class of the delegated action
     */
	protected function delegate($route, $params = array())
	{
		return $this->view = $this->registry->dispatcher->callRoute($route, $params);
	}

    /**
     *
     * @param string $message the error message (optional)
     * @throws MiniMVC_PublicHttpException with 401 status code
     */
	protected function delegate401($message = false)
	{
		throw new MiniMVC_PublicHttpException($message, 401);
	}

    /**
     *
     * @param string $message the error message (optional)
     * @throws MiniMVC_PublicHttpException with 403 status code
     */
	protected function delegate403($message = false)
	{
		throw new MiniMVC_PublicHttpException($message, 403);
	}

    /**
     *
     * @param string $message the error message (optional)
     * @throws MiniMVC_PublicHttpException with 404 status code
     */
	protected function delegate404($message = false)
	{
		throw new MiniMVC_PublicHttpException($message, 404);
	}
	
    /**
     *
     * @param string $message the error message (optional)
     * @param int $code the error code (default 500)
     * @param Exception $previousException a previous exception (optional)
     * @throws MiniMVC_PublicHttpException with 50x status code
     */
	protected function delegate500($message = false, $code = 500, $previousException = null)
	{
		throw new MiniMVC_PublicHttpException($message, $code, $previousException);
	}
}