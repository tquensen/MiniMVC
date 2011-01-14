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

	public function error401Action($params)
	{
        $this->registry->helper->meta->setTitle('Error 401 Unauthorized', false);
        $this->registry->helper->meta->setDescription('');
        header('HTTP/1.1 401 Unauthorized', true, 401);

        if (isset($params['exception']) && $params['exception'] instanceof MiniMVC_HttpException && $params['exception']->getMessage()) {
            $this->view->message = $params['exception']->getMessage();
        } else {
            $this->view->message = 'Error 401 Unauthorized';
        }

        if (isset($params['debug']) && $params['debug'] && isset($params['exception'])) {
            $this->view->e = $params['exception'];
        }
	}
	
	public function error403Action($params)
	{
        $this->registry->helper->meta->setTitle('Error 403 Forbidden', false);
        $this->registry->helper->meta->setDescription('');
		header('HTTP/1.1 403 Forbidden', true, 403);

        if (isset($params['exception']) && $params['exception'] instanceof MiniMVC_HttpException && $params['exception']->getMessage()) {
            $this->view->message = $params['exception']->getMessage();
        } else {
            $this->view->message = 'Error 403 Forbidden';
        }

        if (isset($params['debug']) && $params['debug'] && isset($params['exception'])) {
            $this->view->e = $params['exception'];
        }
	}
	
	public function error404Action($params)
	{
        $this->registry->helper->meta->setTitle('Error 404 Not Found', false);
        $this->registry->helper->meta->setDescription('');
		header('HTTP/1.1 404 Not Found', true, 404);

        if (isset($params['exception']) && $params['exception'] instanceof MiniMVC_HttpException && $params['exception']->getMessage()) {
            $this->view->message = $params['exception']->getMessage();
        } else {
            $this->view->message = 'Error 404 Not Found';
        }

        if (isset($params['debug']) && $params['debug'] && isset($params['exception'])) {
            $this->view->e = $params['exception'];
        }
	}

    public function error500Action($params)
	{
        if (isset($params['exception']) && isset($this->serverErrorCodes[$params['exception']->getCode()])) {
            $this->registry->helper->meta->setTitle('Error '.$this->serverErrorCodes[$params['exception']->getCode()], false);
            $this->view->message = 'Error '.$this->serverErrorCodes[$params['exception']->getCode()];
            header($this->serverErrorCodes[$params['exception']->getCode()], true, $params['exception']->getCode());
        } else {
            $this->registry->helper->meta->setTitle('Error 500 Internal Server Error', false);
            $this->view->message = 'Error 500 Internal Server Error';
            header('500 Internal Server Error', true, 500);
        }

        $this->registry->helper->meta->setDescription('');
        
        if (isset($params['exception']) && $params['exception'] instanceof MiniMVC_HttpException && $params['exception']->getMessage()) {
            $this->view->message = $params['exception']->getMessage();
        }

        if (isset($params['debug']) && $params['debug'] && isset($params['exception'])) {
            $this->view->e = $params['exception'];
        }
	}
}