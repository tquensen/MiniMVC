<?php

class User_Edit_Controller extends MiniMVC_Controller
{

    public function editAction($params)
    {
        $user = UserTable::getInstance()->loadOneById($this->registry->guard->getId());
        if ($user) {
            $this->view->form = $user->getForm(array('type' => 'edit'));
            if ($this->view->form->validate()) {
                $conn = $this->registry->db->getConnection();
                try {
                    $conn->beginTransaction();
                    $this->view->form->updateRecord()->save();
                    $this->registry->guard->email = $user['email'];
                    $this->registry->guard->name = $user['name'];
                    $conn->commit();
                    return $this->view->parse('edit/editSuccess');
                } catch (Doctrine_Exception $e) {
                    $conn->rollback();
                    $this->view->form->setError();
                    $this->view->form->FormCheck->setError('Die Änderungen konnten nicht gespeichert werden! Bitte versuch es später noch eimnmal!');
                }
            }
            return $this->view->parse('edit/editForm');
        } else {
            return $this->delegate401();
        }
    }

    public function editPasswordAction($params)
    {
        $user = UserTable::getInstance()->loadOneById($this->registry->guard->getId());
        if ($user) {
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
