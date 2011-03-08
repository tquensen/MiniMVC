<?php

class User_Events {
    
    public function identifyAuthTokenEvent(sfEvent $event)
    {
        $guard = $event->getSubject();
        if (isset($_REQUEST['auth_token'])) {
            $user = UserTable::getInstance()->loadOneBy('auth_token = ?', $_REQUEST['auth_token']);
            if ($user) {
                $guard->setUser($user->id, $user->role, false);
                $guard->setAuthToken($user->auth_token);
                $guard->isAuthenticatedRequest(true);
                $guard->email = $user->email;
                $guard->slug = $user->slug;
                $guard->name = $user->name;
            }
            return true;
        }
    }
}

