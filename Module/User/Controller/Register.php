<?php
class User_Register_Controller extends MiniMVC_Controller
{
    public function registerAction($params)
    {
        $this->view->form = UserTable::getInstance()->getRecord()->getForm(array('type'=>'register'));
        if ($this->view->form->validate())
        {
            $this->view->form->updateRecord()->save();
            $this->view->user = UserTable::getInstance()->findOneById($this->view->form->getRecord()->get('id'));
            return $this->view->parse('register/registerSuccess');
        }
        return $this->view->parse('register/registerForm');
    }
}
