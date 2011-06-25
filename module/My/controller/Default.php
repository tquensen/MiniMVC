<?php
class My_Default_Controller extends MiniMVC_Controller
{

    public function indexAction($params)
    {
        $this->view->setFile('static/home');
        if ('homeTitle' !== ($title = $this->view->t->get('homeTitle'))) {
            $this->registry->helper->meta->setTitle($title);
        }
        if ('homeDescription' !== ($description = $this->view->t->get('homeDescription'))) {
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
     * $MiniMVC_i18n['My']['PAGENAMETitle'] and $MiniMVC_i18n['My']['PAGENAMEDescription']
     *
     * @param array $params the route params
     */
    public function staticAction($params)
    {
        if (!empty($params['page'])) {
            $page = $params['page'];
            $this->view->setFile('static/'.$page);
            if ($page.'Title' !== ($title = $this->view->t->get($page.'Title'))) {
                $this->registry->helper->meta->setTitle($title);
            }
            if ($page.'Description' !== ($description = $this->view->t->get($page.'Description'))) {
                $this->registry->helper->meta->setDescription($description);
            }
        }
        
        
    }
}
