<?php
class User_Register_Controller extends MiniMVC_Controller
{
    public function registerAction($params)
    {
        $this->view->form = UserTable::getInstance()->getRegisterForm();
        if ($this->view->form->validate())
        {
            $this->view->user = $this->view->form->updateModel();
            $this->view->user->role = $this->registry->rights->getRoleByKeyword('user');
            $this->view->user->save();
            return $this->view->parse('register/registerSuccess');
        }
        return $this->view->parse('register/registerForm');
    }
}
