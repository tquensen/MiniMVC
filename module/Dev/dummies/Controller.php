<?php
class MODULE_CONTROLLER_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        //list view
        /*
        $showPerPage = 20;
        $currentPage = !empty($_GET['p']) ? $_GET['p'] : 1;
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

        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTIndexTitle);
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTIndexMetaDescription);
        */

        //return $this->view->prepare('CONTROLLERLC/index', 'MODULE');
    }

    public function showAction($params)
    {
        if (!$params['model']) {
            return $this->delegate404();
        }
        $this->view->model = $params['model'];

        /*
        $this->registry->helper->meta->setTitle($this->view->model->title);
        $this->registry->helper->meta->setDescription($this->view->model->description);
        */

        //return $this->view->prepare('CONTROLLERLC/show', 'MODULE');
    }

    public function newAction($params)
    {
        /*
        $form = CONTROLLERTable::getInstance()->getForm(null, array(
            'route' => 'MODLC.CONTROLLERLCFIRSTCreate'
        ));

        $this->view->form = $form;

        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTNewTitle);
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTNewMetaDescription);

         */


        //return $this->view->prepare('CONTROLLERLC/new', 'MODULE');
    }

    public function createAction($params)
    {
        /*
        $form = CONTROLLERTable::getInstance()->getForm();
        $model = $form->getModel();
        $success = false;

        if ($form->validate())
        {
            $form->updateModel();
            if ($model->save()) {
                $success = true;
                if ($params['_format'] == 'html') {
                    $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTCreateSuccessMessage, 'success');
                    $form->successRedirect('MODLC.CONTROLLERLCFIRSTShow', array('id' => $model->id));
                }
            } else {
                $form->setError($this->view->t->CONTROLLERLCFIRSTCreateErrorMessage);
            }
        }

        if ($params['_format'] == 'html') {
            $form->errorRedirect('MODLC.CONTROLLERLCFIRSTNew');
        }

        $this->view->form = $form;
        $this->view->model = $model;
        $this->view->success = $success;
         */
    }

    public function editAction($params)
    {
        /*
        if (!$params['model']) {
            return $this->delegate404();
        }

        $form = CONTROLLERTable::getInstance()->getForm($params['model'], array(
            'route' => 'MODLC.CONTROLLERLCFIRSTUpdate',
            'parameter => array('id' => $model->id)
        ));

        $this->view->form = $form;

        $this->registry->helper->meta->setTitle($this->view->t->CONTROLLERLCFIRSTEditTitle);
        $this->registry->helper->meta->setDescription($this->view->t->CONTROLLERLCFIRSTEditMetaDescription);

         */

        //return $this->view->prepare('CONTROLLERLC/edit', 'MODULE');
    }

    public function updateAction($params)
    {
        /*
        if (!$params['model']) {
            return $this->delegate404();
        }

        $form = CONTROLLERTable::getInstance()->getForm($params['model']);
        $model = $form->getModel();
        $success = false;

        if ($form->validate())
        {
            $form->updateModel();
            if ($model->save()) {
                $success = true;
                if ($params['_format'] == 'html') {
                    $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTUpdateSuccessMessage, 'success');
                    $form->successRedirect('MODLC.CONTROLLERLCFIRSTShow', array('id' => $model->id));
                }
            } else {
                $this->view->form->setError($this->view->t->CONTROLLERLCFIRSTUpdateErrorMessage);
            }
        }

        if ($params['_format'] == 'html') {
            $form->errorRedirect('MODLC.CONTROLLERLCFIRSTUpdate', array('id' => $model->id));
        }

        $this->view->form = $form;
        $this->view->model = $model;
        $this->view->success = $success;
         */
    }

    public function deleteAction($params)
    {
        /*

        if (!$params['model']) {
            return $this->delegate404();
        }
        if (!$this->registry->guard->checkCsrfProtection(false)) {
            return $this->delegate403();
        }

        $this->view->success = $params['model']->delete();

        if ($this->view->success) {
            if ($params['_format'] == 'html') {
                $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTDeleteSuccessMessage, 'success');
                return $this->redirect('MODLC.CONTROLLERLCFIRSTIndex');
            }
        } else {
            if ($params['_format'] == 'html') {
                $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTDeleteErrorMessage, 'error');
                return $this->redirect('MODLC.CONTROLLERLCFIRSTIndex');
            }
        }

         */
    }

    public function widgetAction($params)
    {
        //return $this->view->prepare('CONTROLLERLC/widget', 'MODULE');
    }
}
