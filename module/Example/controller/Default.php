<?php
class Example_Default_Controller extends MiniMVC_Controller
{

    public function indexAction($params)
    {
        
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
        if (!empty($params['view'])) {
            $this->view->setFile('static/'.$params['view']);
        }
    }
}
