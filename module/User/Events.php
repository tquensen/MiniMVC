<?php

class User_Events {
    
    public function identifyAuthTokenEvent(sfEvent $event)
    {
        $guard = $event->getSubject();
        $token = $event['authToken'];

        $user = UserTable::getInstance()->loadOneBy('auth_token = ?', $token);
        if ($user) {
            $guard->setUser($user->id, $user->role);
            $guard->setAuthToken($user->auth_token);
            $guard->email = $user->email;
            $guard->slug = $user->slug;
            $guard->name = $user->name;
        }
    }
}

