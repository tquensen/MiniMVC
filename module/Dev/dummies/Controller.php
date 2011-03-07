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
        if ($this->view->selectCache(
            array( //dependencies
                'name' => 'MODLC.CONTROLLERLC/index',
                'role' => $this->registry->guard->getRole()
            ),
            array( //tokens
                'MODLC.CONTROLLERLCFIRSTIndex'
            ), true, 3600 //bindtourl = true, expires in 3600 seconds
        )) {
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
        if ($this->view->selectCache(
            array( //dependencies
                'name' => 'MODLC.CONTROLLERLC/show',
                'role' => $this->registry->guard->getRole()
            ),
            array( //tokens
                'MODLC.CONTROLLERLCFIRSTShow.'.$model->slug,
                'MODLC.CONTROLLERLCFIRSTShow'
            ), true, 3600 //bindtourl = true, expires in 3600 seconds
        )) {
            return $this->view->prepareCache();
        }
        */

        $this->view->model= $model;
    }

    public function createAction($params)
    {
        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTCreateTitle);
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTCreateMetaDescription);

        $form = CONTROLLERTable::getInstance()->getForm(null, array(
            'route' => 'MODLC.CONTROLLERLCFIRSTCreate'
        ));
        $success = false;

        if ($form->wasSubmitted()) {
            $model = $form->getModel();
            $message = '';

            if ($form->validate()) {
                $form->updateModel();
                if ($model->save()) {
                    $success = true;
                    $message = $this->view->t->CONTROLLERLCFIRSTCreateSuccessMessage(array('title' => htmlspecialchars($model->title)));

                    //clear the index cache (and other cached pages if needed)
                    //$this->view->deleteCache('MODLC.CONTROLLERLCFIRSTIndex');

                    if ($this->registry->layout->getFormat() === null) {
                        $this->registry->helper->messages->add($message, 'success');
                        return $this->redirect('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug));
                    }
                } else {
                    $form->setError($this->view->t->CONTROLLERLCFIRSTCreateErrorMessage);
                }
            }
            
            $this->view->model = $model;
            $this->view->message = $message;
        }

        $this->view->success = $success;
        $this->view->form = $form;
    }

    public function updateAction($params)
    {
        if (!$params['model']) {
            return $this->delegate404();
        }

        $model = $params['model'];

        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTUpdateTitle(array('title' => htmlspecialchars($model->title))));
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTUpdateMetaDescription(array('title' => htmlspecialchars($model->title))));
        
        $form = CONTROLLERTable::getInstance()->getForm($model, array(
            'route' => 'MODLC.CONTROLLERLCFIRSTUpdate',
            'parameter' => array('slug' => $model->slug)
        ));
        $success = false;

        if ($form->wasSubmitted()) {
            $message = '';

            if ($form->validate()) {
                $form->updateModel();
                if ($model->save()) {
                    $success = true;
                    $message = $this->view->t->CONTROLLERLCFIRSTUpdateSuccessMessage(array('title' => htmlspecialchars($model->title)));

                    //clear the index cache and the cache of this model (and other cached pages if needed)
                    //$this->view->deleteCache(array('MODLC.CONTROLLERLCFIRSTIndex', 'MODLC.CONTROLLERLCFIRSTShow.'.$model->slug));

                    if ($this->registry->layout->getFormat() === null) {
                        $this->registry->helper->messages->add($message, 'success');
                        return $this->redirect('MODLC.CONTROLLERLCFIRSTShow', array('slug' => $model->slug));
                    }
                } else {
                    $this->view->form->setError($this->view->t->CONTROLLERLCFIRSTUpdateErrorMessage);
                }
            }

            $this->view->message = $message;
        }

        $this->view->success = $success;
        $this->view->model = $model;
        $this->view->form = $form;
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
            //$this->view->deleteCache(array('MODLC.CONTROLLERLCFIRSTIndex', 'MODLC.CONTROLLERLCFIRSTShow.'.$model->slug));

            if ($this->registry->layout->getFormat() === null) {
                $this->registry->helper->messages->add($message, 'success');
                return $this->redirect('MODLC.CONTROLLERLCFIRSTIndex');
            }
        } else {
            $message = $this->view->t->CONTROLLERLCFIRSTDeleteErrorMessage(array('title' => htmlspecialchars($model->title)));
            if ($this->registry->layout->getFormat() === null) {
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
