<?php
class MODULE_CONTROLLER_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        return $this->view->parse();
    }

    public function createAction($params)
    {
        /*
        $this->view->form = MODULETable::getInstance()->getForm();
        if ($this->view->form->validate())
        {
            $model = $this->view->form->updateModel();
            $mode->save();
            return $this->redirect('MODLC.defaultShow', array('id' => $model->id));
        }
         */
        return $this->view->parse();
    }
}
