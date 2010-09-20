<?php 
class Core_Error_Controller extends MiniMVC_Controller
{
	public function error401Action()
	{
		
	}
	
	public function error403Action()
	{
		
	}
	
	public function error404Action()
	{
		
	}

    public function error500Action($params)
	{
        $this->view->e = $params['exception'];
        if (isset($params['debug']) && $params['debug']) {
            $this->view->setFile('error/error500debug');
        }
	}
}