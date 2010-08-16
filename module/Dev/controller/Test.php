<?php
class Dev_Test_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        return $this->view->parse();
    }
}
