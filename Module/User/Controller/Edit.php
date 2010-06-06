<?php

class User_Edit_Controller extends MiniMVC_Controller
{

    public function editAction($params)
    {
        $user = Doctrine_Core::getTable('User')->findOneById($this->registry->guard->getId());
        if ($user && $user->exists()) {
            $this->view->form = $user->getForm(array('type' => 'edit'));
            if ($this->view->form->validate()) {
                $this->view->form->updateRecord()->save();
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
        $user = Doctrine_Core::getTable('User')->findOneById($this->registry->guard->getId());
        if ($user && $user->exists()) {
            $this->view->form = $user->getForm(array('type' => 'editPassword'));
            if ($this->view->form->validate()) {
                $record = $this->view->form->updateRecord();
                $record->updatePassword();
                $record->save();
                return $this->view->parse('edit/editPasswordSuccess');
            }
            return $this->view->parse('edit/editPasswordForm');
        } else {
            return $this->delegate401();
        }
    }

}
