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
                return $this->view->parse('edit/editSuccess');
            }
            return $this->view->parse('edit/editForm');
        } else {
            return $this->delegate401();
        }
    }

    public function editPasswordAction($params)
    {
        $user = UserTable::getInstance()->loadOne($this->registry->guard->getId());
        if ($user) {
            $this->view->form = UserTable::getInstance()->getEditPasswordForm($user);
            if ($this->view->form->validate()) {
                $this->view->form->updateModel()->save();
                return $this->view->parse('edit/editPasswordSuccess');
            }
            return $this->view->parse('edit/editPasswordForm');
        } else {
            return $this->delegate401();
        }
    }

}
