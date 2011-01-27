<?php
class User extends UserBase
{
    
    public function preSave()
    {
        $this->updated_at = time();

        if ($this->password != $this->getDatabaseProperty('password')) {
            $this->salt = $this->generateSalt();
            $this->password = hash('sha256', $this->password . $this->salt);
        }

        if ($this->isNew()) {
            $this->slug = $this->getTable()->generateSlug($this, $this->name, 'slug', 32);
            $this->created_at = time();
            $start = rand(10, 30);
            $this->auth_token = $this->slug.'_'.substr(hash('sha256', $this->slug.time().$this->salt.rand(1000000,9999999)), $start, 32);
            $this->role = MiniMVC_Registry::getInstance()->rights->getRoleByKeyword('user');
        }
        
        
    }

    protected function generateSalt()
    {
        $salt = '';
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"$%&/()=?@<>|}][{+#-.,*_:';

        $salt_length = rand(48,64);
        $chars_length = mb_strlen($chars, 'UTF-8') - 1;
        for ($i=0;$i<$salt_length;$i++)
        {
            $salt .= mb_substr($chars,rand(0, $chars_length), 1, 'UTF-8');
        }
        return $salt;
    }

    public function checkPassword($password)
    {
        return hash('sha256', $password.$this->salt) == $this->password;
    }

    public function checkLoginPasswordCallback($validator, $isValid)
    {
        if (!$isValid) {
            return true;
        }

        $user = $this->getTable()->loadOneBy('email = ?', $validator->getForm()->email->value);
        if (!$user || !$user->checkPassword($validator->getForm()->password->value)) {
            return false;
        }
        $validator->getForm()->setModel($user);
        return true;
    }
    
}