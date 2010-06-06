<?php
class User_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {

        return $this->view->parse('default/index');
    }
}
