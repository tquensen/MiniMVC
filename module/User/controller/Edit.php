<?php

class User_Edit_Controller extends MiniMVC_Controller
{

    public function editAction($params)
    {
        $user = UserTable::getInstance()->loadOne($this->registry->guard->getId());
        if ($user) {
            $this->view->form = UserTable::getInstance()->getEditForm($user);
            if ($this->view->form->validate()) {
                $this->view->form->updateModel();
                if (!$user->save()) {
                    $this->view->form->validate();
                    $this->view->form->errorRedirect();
                }
                $this->registry->guard->email = $user['email'];
                $this->registry->guard->name = $user['name'];
                $this->view->setFile('edit/editSuccess');
                return;
            }
            $this->view->setFile('edit/editForm');
        } else {
            $this->delegate401();
        }
    }

    public function editPasswordAction($params)
    {
        $user = UserTable::getInstance()->loadOne($this->registry->guard->getId());
        if ($user) {
            $this->view->form = UserTable::getInstance()->getEditPasswordForm($user);
            if ($this->view->form->validate()) {
                $this->view->form->updateModel()->save();
                $this->view->setFile('edit/editPasswordSuccess');
                return;
            }
            $this->view->setFile('edit/editPasswordForm');
        } else {
            $this->delegate401();
        }
    }

}
