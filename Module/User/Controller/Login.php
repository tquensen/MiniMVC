<?php

class User_Login_Controller extends MiniMVC_Controller
{

    public function loginAction($params)
    {
        $this->view->form = UserTable::getInstance()->getRecord()->getForm(array('type' => 'login'));
        if ($this->view->form->validate()) {
            $user = UserTable::getInstance()->findOneBy('email', $this->view->form->email->value);
            if (!$user || ! $user->exists() || ! $user->checkPassword($this->view->form->password->value)) {
                $this->view->form->setError();
                $this->view->form->password->setError('Der Username oder das Passwort ist ungültig');
            } else {
                $guard = $this->registry->guard;
                $guard->setUser($user['id'], $user['role']);
                $guard->email = $user['email'];
                $guard->name = $user['name'];

                if (isset($this->registry->settings->config['user']['loginRedirect']) && $this->registry->settings->config['user']['loginRedirect']) {
                    $redirect = $this->registry->settings->config['user']['loginRedirect'];
                    if (is_array($redirect)) {
                        if (isset($redirect['route'])) {
                            return $this->redirect($redirect['route'], (isset($redirect['parameter'])) ? $redirect['parameter'] : array(), (isset($redirect['app'])) ? $redirect['app'] : null);
                        }
                    } else {
                        return $this->redirect($redirect);
                    }
                }

                return $this->view->parse('login/loginSuccess');
            }
        }
        return $this->view->parse('login/loginForm');
    }

    public function logoutAction($params)
    {
        $this->view->form = UserTable::getInstance()->getRecord()->getForm(array('type' => 'logout'));
        if ($this->view->form->validate()) {
            $this->registry->guard->setUser();
            if (isset($this->registry->settings->config['user']['logoutRedirect']) && $this->registry->settings->config['user']['logoutRedirect']) {
                $redirect = $this->registry->settings->config['user']['logoutRedirect'];
                if (is_array($redirect)) {
                    if (isset($redirect['route'])) {
                        return $this->redirect($redirect['route'], (isset($redirect['parameter'])) ? $redirect['parameter'] : array(), (isset($redirect['app'])) ? $redirect['app'] : null);
                    }
                } else {
                    return $this->redirect($redirect);
                }
            }
            return $this->view->parse('login/logoutSuccess');
        }
        return $this->view->parse('login/logoutForm');
    }

}
