<?php
class MODULE_Default_Controller extends MiniMVC_Controller
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
                $this->registry->helper->url->get('MODLC.defaultIndex') . '(?p={page})',
                $currentPage,
                7,
                false
        );

        $this->registry->helper->meta->setTitle($this->view->t->defaultIndexTitle);
        $this->registry->helper->meta->setDescription($this->view->t->defaultIndexMetaDescription);

        */

        //return $this->view->prepare('default/index', 'MODULE');
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

        //return $this->view->prepare('default/show', 'MODULE');
    }
    
    public function newAction($params)
    {
        /*
        $form = MODULETable::getInstance()->getForm(null, array(
            'route' => 'MODLC.defaultCreate'
        ));

        $this->view->form = $form;

        $this->registry->helper->meta->setTitle($this->view->t->defaultNewTitle);
        $this->registry->helper->meta->setDescription($this->view->t->defaultNewMetaDescription);

         */


        //return $this->view->prepare('default/new', 'MODULE');
    }

    public function createAction($params)
    {
        /*
        $form = MODULETable::getInstance()->getForm();
        if ($form->validate())
        {
            $model = $form->updateModel();
            if ($model->save()) {
                $this->registry->helper->messages->add($this->view->t->defaultCreateSuccessMessage, 'success');
                $form->successRedirect('MODLC.defaultShow', array('id' => $model->id));
            }

            $form->setError($this->view->t->defaultCreateErrorMessage);
        }

        $form->errorRedirect('MODLC.defaultNew');
         */
    }

    public function editAction($params)
    {
        /*
        if (!$params['model']) {
            return $this->delegate404();
        }

        $form = MODULETable::getInstance()->getForm($params['model'], array(
            'route' => 'MODLC.defaultUpdate',
            'parameter => array('id' => $model->id)
        ));

        $this->view->form = $form;

        $this->registry->helper->meta->setTitle($this->view->t->defaultEditTitle);
        $this->registry->helper->meta->setDescription($this->view->t->defaultEditMetaDescription);

         */

        //return $this->view->prepare('default/edit', 'MODULE');
    }
    
    public function updateAction($params)
    {
        /*
        if (!$params['model']) {
            return $this->delegate404();
        }

        $form = MODULETable::getInstance()->getForm($params['model']);

        if ($form->validate())
        {
            $model = $form->updateModel();
            if ($model->save()) {
                $this->registry->helper->messages->add($this->view->t->defaultUpdateSuccessMessage, 'success');
                $form->successRedirect('MODLC.defaultShow', array('id' => $model->id));
            }

            $this->view->form->setError($this->view->t->defaultUpdateErrorMessage);
        }

        $form->errorRedirect('MODLC.defaultUpdate', array('id' => $model->id));
         */
    }

    public function deleteAction($params)
    {
        /*
        if (!$params['model']) {
            return $this->delegate404();
        }
        if (!$this->registry->guard->checkCsrfProtection(false) || !$params['model']->delete()) {
            $this->registry->helper->messages->add($this->view->t->defaultDeleteErrorMessage, 'error');
        } else {
            $this->registry->helper->messages->add($this->view->t->defaultDeleteSuccessMessage, 'success');
        }

        return $this->redirect('MODLC.defaultIndex');
         */
    }

    public function widgetAction($params)
    {
        //return $this->view->prepare('default/widget', 'MODULE');
    }
}
