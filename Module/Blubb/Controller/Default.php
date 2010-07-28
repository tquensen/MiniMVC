<?php
class Blubb_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        return $this->view->parse('default/index');
    }

    public function widgetAction($params)
    {
        return $this->view->parse('default/widget');
    }
}
