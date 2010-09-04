<?php
class My_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        return $this->view->parse();
    }

    public function createAction($params)
    {
        return $this->view->parse();
    }

    public function widgetAction($params)
    {
        return $this->view->parse();
    }
}
