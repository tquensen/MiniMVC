<?php

class User_Login_Controller extends MiniMVC_Controller
{

    public function loginAction($params)
    {
        $this->view->form = UserTable::getInstance()->getLoginForm(isset($params['widget']) ? $params['widget'] : false);
        if ($this->view->form->validate()) {
            $user = $this->view->form->getModel();
            $guard = $this->registry->guard;
            $guard->setUser($user->id, $user->role);
            $guard->email = $user->email;
            $guard->name = $user->name;
            $guard->slug = $user->slug;

            if ($redirect = $this->registry->settings->get('config/user/loginRedirect')) {
                if (is_array($redirect)) {
                    if (isset($redirect['route'])) {
                        return $this->redirect($redirect['route'], (isset($redirect['parameter'])) ? $redirect['parameter'] : array(), (isset($redirect['app'])) ? $redirect['app'] : null);
                    }
                } else {
                    return $this->redirect($redirect);
                }
            }
            return $this->view->prepare('login/loginSuccess');
        }
        return $this->view->prepare('login/loginForm');
    }

    public function logoutAction($params)
    {
        $this->view->form = UserTable::getInstance()->getLogoutForm(isset($params['widget']) ? $params['widget'] : false);
        if ($this->view->form->validate()) {
            $this->registry->guard->setUser();
            if ($redirect = $this->registry->settings->get('config/user/logoutRedirect')) {
                if (is_array($redirect)) {
                    if (isset($redirect['route'])) {
                        return $this->redirect($redirect['route'], (isset($redirect['parameter'])) ? $redirect['parameter'] : array(), (isset($redirect['app'])) ? $redirect['app'] : null);
                    }
                } else {
                    return $this->redirect($redirect);
                }
            }
            return $this->view->prepare('login/logoutSuccess');
        }
        return $this->view->prepare('login/logoutForm');
    }

}
