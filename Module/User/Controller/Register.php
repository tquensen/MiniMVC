<?php
class User_Register_Controller extends MiniMVC_Controller
{
    public function registerAction($params)
    {
        $this->view->form = Doctrine_Core::getTable('User')->getRecord()->getForm(array('type'=>'register'));
        if ($this->view->form->validate())
        {
            $this->view->form->updateRecord()->save();
            $this->view->user = Doctrine_Core::getTable('User')->findOneById($this->view->form->getRecord()->get('id'));
            return $this->view->parse('register/registerSuccess');
        }
        return $this->view->parse('register/registerForm');
    }
}
