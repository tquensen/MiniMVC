<?php
class My_Default_Controller extends MiniMVC_Controller
{

    public function indexAction($params)
    {
        $this->view->setFile('static/home');
    }

    //
    /**
     * Load and display the view with the given name
     *
     * callable via BASEURL/page/PAGENAME where PAGENAME is a valid view
     * in view/static/PAGENAME.php
     *
     * @param array $params the route params
     */
    public function staticAction($params)
    {
        if (!empty($params['page'])) {
            $this->view->setFile('static/'.$params['page']);
        }
    }
}
