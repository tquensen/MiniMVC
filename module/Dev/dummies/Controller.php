<?php
class MODULE_CONTROLLER_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        //list view
        /*
        $showPerPage = 20;
        $currentPage = !empty($_GET['p']) ? $_GET['p'] : 1;
        $query = MODULETable::getInstance()->load(null, null, 'id DESC', $showPerPage, ($currentPage - 1) * $showPerPage, 'query');
        
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
        if ($form->validate())
        {
            $model = $form->updateModel();
            if ($model->save()) {
                $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTCreateSuccessMessage, 'success');
                $form->successRedirect('MODLC.CONTROLLERLCFIRSTShow', array('id' => $model->id));
            }

            $form->setError($this->view->t->CONTROLLERLCFIRSTCreateErrorMessage);
        }

        $form->errorRedirect('MODLC.CONTROLLERLCFIRSTNew');
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

        if ($form->validate())
        {
            $model = $form->updateModel();
            if ($model->save()) {
                $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTUpdateSuccessMessage, 'success');
                $form->successRedirect('MODLC.CONTROLLERLCFIRSTShow', array('id' => $model->id));
            }

            $this->view->form->setError($this->view->t->CONTROLLERLCFIRSTUpdateErrorMessage);
        }

        $form->errorRedirect('MODLC.CONTROLLERLCFIRSTUpdate', array('id' => $model->id));
         */
    }

    public function deleteAction($params)
    {
        /*

        if (!$params['model']) {
            return $this->delegate404();
        }
        if (!$this->registry->guard->checkCsrfProtection(false) || !$params['model']->delete()) {
            $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTDeleteErrorMessage, 'error');
        } else {
            $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTDeleteSuccessMessage, 'success');
        }

        return $this->redirect('MODLC.CONTROLLERLCFIRSTIndex');
         */
    }

    public function widgetAction($params)
    {
        //return $this->view->prepare('CONTROLLERLC/widget', 'MODULE');
    }
}
