<?php 
class Core_Error_Controller extends MiniMVC_Controller
{
	public function error401Action()
	{
		return $this->view->parse('Error/error401');
	}
	
	public function error403Action()
	{
		return $this->view->parse('Error/error403');
	}
	
	public function error404Action()
	{
		return $this->view->parse('Error/error404');
	}

    public function error500Action($params)
	{
        $this->view->e = $params['exception'];
        if (isset($params['debug']) && $params['debug']) {
            return $this->view->parse('Error/error500debug');
        }
		return $this->view->parse('Error/error500');
	}
}