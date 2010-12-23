<?php 
class Core_Error_Controller extends MiniMVC_Controller
{
    protected $serverErrorCodes = array(
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Time-out',
        505 => '505 HTTP Version not supported',
        506 => '506 Variant Also Negotiates',
        507 => '507 Insufficient Storage',
        509 => '509 Bandwidth Limit Exceeded',
        510 => '510 Not Extended',
    );

	public function error401Action()
	{
        $this->registry->helper->meta->setTitle('Error 401 Unauthorized', false);
        $this->registry->helper->meta->setDescription('');
        header('HTTP/1.1 401 Unauthorized', true, 401);
	}
	
	public function error403Action()
	{
        $this->registry->helper->meta->setTitle('Error 403 Forbidden', false);
        $this->registry->helper->meta->setDescription('');
		header('HTTP/1.1 403 Forbidden', true, 403);
	}
	
	public function error404Action($params)
	{
        $this->registry->helper->meta->setTitle('Error 404 Not Found', false);
        $this->registry->helper->meta->setDescription('');
		header('HTTP/1.1 404 Not Found', true, 404);

        if (isset($params['debug']) && $params['debug'] && isset($params['exception'])) {
            $this->view->e = $params['exception'];
            $this->view->setFile('error/error404debug');
        }
	}

    public function error500Action($params)
	{
        if (isset($params['exception']) && isset($this->serverErrorCodes[$params['exception']->getCode()])) {
            $this->registry->helper->meta->setTitle('Error '.$this->serverErrorCodes[$params['exception']->getCode()], false);
            header($this->serverErrorCodes[$params['exception']->getCode()], true, $params['exception']->getCode());
        } else {
            $this->registry->helper->meta->setTitle('Error 500 Internal Server Error', false);
            header('500 Internal Server Error', true, 500);
        }

        $this->registry->helper->meta->setDescription('');
        
        if (isset($params['debug']) && $params['debug'] && isset($params['exception'])) {
            $this->view->e = $params['exception'];
            $this->view->setFile('error/error500debug');
        }
	}
}