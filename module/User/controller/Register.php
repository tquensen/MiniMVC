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
            if (!$this->view->user->save()) {
                $this->view->form->validate();
                $this->view->form->errorRedirect();
            }
            return $this->view->setFile('register/registerSuccess');
        }
        return $this->view->setFile('register/registerForm');
    }
}
