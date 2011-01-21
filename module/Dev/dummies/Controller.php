<?php
class MODULE_CONTROLLER_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {     
        $showPerPage = 20;
        $currentPage = !empty($_GET['p']) ? $_GET['p'] : 1;

        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTIndexTitle(array('page' => $currentPage)));
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTIndexMetaDescription);

        //activate the cache - different cache for different roles (some roles have a create-link in the view)
        /*
        if ($this->view->selectCache($this->registry->helper->cache->get(
                'MODLC.CONTROLLERLC/index',
                array('role' => $this->registry->guard->getRole()),
                'MODLC.CONTROLLERLCFIRSTIndex'
        ))) {
            return $this->view->prepareCache();
        }
         */

        $query = CONTROLLERTable::getInstance()->load(null, null, 'id DESC', $showPerPage, ($currentPage - 1) * $showPerPage, 'query');
        
        $this->view->entries = $query->build();

        $this->view->pager = $this->registry->helper->pager->get(
                $query->count(),
                $showPerPage,
                $this->registry->helper->url->get('MODLC.CONTROLLERLCFIRSTIndex') . '(?p={page})',
                $currentPage,
                7,
                false
        );
    }

    public function showAction($params)
    {
        if (!$params['model']) {
            return $this->delegate404();
        }

        $model = $params['model'];

        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTShowTitle(array('title' => htmlspecialchars($model->title))));
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTShowMetaDescription(array('title' => htmlspecialchars($model->title), 'description' => strip_tags($model->description))));

        //activate the cache
        /*
        if ($this->view->selectCache($this->registry->helper->cache->get(
                'MODLC.CONTROLLERLC/show',
                array('role' => $this->registry->guard->getRole()),
                array('MODLC.CONTROLLERLCFIRSTShow'.$model->slug, 'MODLC.CONTROLLERLCFIRSTShow')
        ))) {
            return $this->view->prepareCache();
        }
        */

        $this->view->model= $model;
    }

    public function newAction($params)
    {
        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTNewTitle);
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTNewMetaDescription);

        $form = CONTROLLERTable::getInstance()->getForm(null, array(
            'route' => 'MODLC.CONTROLLERLCFIRSTCreate'
        ));

        $this->view->form = $form;
    }

    public function createAction($params)
    {
        $form = CONTROLLERTable::getInstance()->getForm(null, array(
            'route' => 'MODLC.CONTROLLERLCFIRSTCreate'
        ));

        $model = $form->getModel();
        $success = false;
        $message = '';

        if ($form->validate())
        {
            $form->updateModel();
            if ($model->save()) {
                $success = true;
                $message = $this->view->t->CONTROLLERLCFIRSTCreateSuccessMessage(array('title' => htmlspecialchars($model->title)));

                //clear the index cache (and other cached pages if needed)
                //$this->view->deleteCache('MODLC.CONTROLLERLCFIRSTIndex');

                if ($this->registry->layout->getFormat() === null) {
                    $this->registry->helper->messages->add($message, 'success');
                    $form->successRedirect('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug));
                }
            } else {
                $form->setError($this->view->t->CONTROLLERLCFIRSTCreateErrorMessage);
            }
        }

        if ($this->registry->layout->getFormat() === null) {
            $form->errorRedirect('MODLC.CONTROLLERLCFIRSTNew');
        }

        $this->view->form = $form;
        $this->view->model = $model;
        $this->view->success = $success;
        $this->view->message = $message;
    }

    public function editAction($params)
    {
        if (!$params['model']) {
            return $this->delegate404();
        }

        $model = $params['model'];

        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTEditTitle(array('title' => htmlspecialchars($model->title))));
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTEditMetaDescription(array('title' => htmlspecialchars($model->title))));

        $form = CONTROLLERTable::getInstance()->getForm($model, array(
            'route' => 'MODLC.CONTROLLERLCFIRSTUpdate',
            'parameter' => array('slug' => $model->slug)
        ));

        $this->view->form = $form;
        $this->view->model = $model;
    }

    public function updateAction($params)
    {
        if (!$params['model']) {
            return $this->delegate404();
        }

        $model = $params['model'];
        
        $form = CONTROLLERTable::getInstance()->getForm($model, array(
            'route' => 'MODLC.CONTROLLERLCFIRSTUpdate',
            'parameter' => array('slug' => $model->slug)
        ));
        $success = false;
        $message = '';

        if ($form->validate())
        {
            $form->updateModel();
            if ($model->save()) {
                $success = true;
                $message = $this->view->t->CONTROLLERLCFIRSTUpdateSuccessMessage(array('title' => htmlspecialchars($model->title)));

                //clear the index cache and the cache of this model (and other cached pages if needed)
                //$this->view->deleteCache(array('MODLC.CONTROLLERLCFIRSTIndex', 'MODLC.CONTROLLERLCFIRSTShow'.$model->slug));

                if ($this->registry->layout->getFormat() === null) {
                    $this->registry->helper->messages->add($message, 'success');
                    $form->successRedirect('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug));
                }
            } else {
                $this->view->form->setError($this->view->t->CONTROLLERLCFIRSTUpdateErrorMessage);
            }
        }

        if ($this->registry->layout->getFormat() === null) {
            $form->errorRedirect('MODLC.CONTROLLERLCFIRSTEdit', array('slug' => $model->slug));
        }

        $this->view->form = $form;
        $this->view->model = $model;
        $this->view->success = $success;
        $this->view->message = $message;
    }

    public function deleteAction($params)
    {
        if (!$params['model']) {
            return $this->delegate404();
        }

        $model = $params['model'];
        $success = $model->delete();

        if ($success) {
            $message = $this->view->t->CONTROLLERLCFIRSTDeleteSuccessMessage(array('title' => htmlspecialchars($model->title)));

            //clear the index cache and the cache of this model (and other cached pages if needed)
            //$this->view->deleteCache(array('MODLC.CONTROLLERLCFIRSTIndex', 'MODLC.CONTROLLERLCFIRSTShow'.$model->slug));

            if ($params['_format'] == 'default') {
                $this->registry->helper->messages->add($message, 'success');
                return $this->redirect('MODLC.CONTROLLERLCFIRSTIndex');
            }
        } else {
            $message = $this->view->t->CONTROLLERLCFIRSTDeleteErrorMessage(array('title' => htmlspecialchars($model->title)));
            if ($params['_format'] == 'default') {
               $this->registry->helper->messages->add($message, 'error');
                return $this->redirect('MODLC.CONTROLLERLCFIRSTIndex');
            }
        }

        $this->view->model = $model;
        $this->view->success = $success;
        $this->view->message = $message;
    }

    public function widgetAction($params)
    {
    }
}
