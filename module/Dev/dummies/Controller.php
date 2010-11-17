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
        */
        
        //return $this->view->prepare('CONTROLLERLC/index', 'MODULE');
    }

    public function showAction($params)
    {
        if (!$params['model']) {
            return $this->delegate404();
        }
        $this->view->model = $params['model'];

        //return $this->view->prepare('CONTROLLERLC/show', 'MODULE');
    }

    public function createAction($params)
    {
        /*
        $this->view->form = MODULETable::getInstance()->getForm();
        if ($this->view->form->validate())
        {
            $model = $this->view->form->updateModel();
            if (!$model->save()) {
                $this->view->form->setError($this->view->t->CONTROLLERLCFIRSTFormSaveError);
                $this->view->form->errorRedirect();
            }
            return $this->redirect('MODLC.CONTROLLERLCFIRSTShow', array('id' => $model->id));
        }

        $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTCreateSuccessMessage, 'success');
         */

        //return $this->view->prepare('CONTROLLERLC/create', 'MODULE');
    }

    public function editAction($params)
    {
        /*
        if (!$params['model']) {
            return $this->delegate404();
        }
        $this->view->form = MODULETable::getInstance()->getForm($params['model']);
        if ($this->view->form->validate())
        {
            $model = $this->view->form->updateModel();
            if (!$model->save()) {
                $this->view->form->setError($this->view->t->CONTROLLERLCFIRSTFormSaveError);
                $this->view->form->errorRedirect();
            }
            return $this->redirect('MODLC.CONTROLLERLCFIRSTShow', array('id' => $model->id));
        }

        $this->registry->helper->messages->add($this->view->t->CONTROLLERLCFIRSTEditSuccessMessage, 'success');

         */

        //return $this->view->prepare('CONTROLLERLC/edit', 'MODULE');
    }

    public function deleteAction($params)
    {
        /*
        if (!$params['model']) {
            return $this->delegate404();
        }
        if (!$params['model']->delete()) {
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
