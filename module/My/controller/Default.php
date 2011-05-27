<?php
class My_Default_Controller extends MiniMVC_Controller
{

    public function indexAction($params)
    {
        $this->view->setFile('static/home');
        if ($title = $this->view->t->get('home_title')) {
            $this->registry->helper->meta->setTitle($title);
        }
        if ($description = $this->view->t->get('home_description')) {
            $this->registry->helper->meta->setDescription($description);
        }
    }

    //
    /**
     * Load and display the view with the given name
     *
     * callable via BASEURL/page/PAGENAME where PAGENAME is a valid view
     * in view/static/PAGENAME.php
     * 
     * add title and meta description in i18n files as 
     * $MiniMVC_i18n['My']['PAGENAME_title'] and $MiniMVC_i18n['My']['PAGENAME_description']
     *
     * @param array $params the route params
     */
    public function staticAction($params)
    {
        if (!empty($params['page'])) {
            $page = $params['page'];
            $this->view->setFile('static/'.$page);
            if ($title = $this->view->t->get($page.'_title')) {
                $this->registry->helper->meta->setTitle($title);
            }
            if ($description = $this->view->t->get($page.'_description')) {
                $this->registry->helper->meta->setDescription($description);
            }
        }
        
        
    }
}
