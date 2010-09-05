<?php
/**
 * @property UserTable $_table
 * @method UserTable getTable()
 */
class User extends MiniMVC_Model
{
    public function preInsert()
    {
        $this->slug = $this->getTable()->generateSlug($this, $this->name, 'slug');
    }

    public function preSave()
    {
        if ($this->password != $this->getDatabaseProperty('password')) {
            $this->salt = $this->generateSalt();
            $this->password = md5($this->password . $this->salt);
        }
    }

    protected function generateSalt()
    {
        $salt = '';
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"$%&/()=?@<>|}][{+#-.,*_:';

        $salt_length = rand(24,32);
        $chars_length = mb_strlen($chars, 'UTF-8') - 1;
        for ($i=0;$i<$salt_length;$i++)
        {
            $salt .= mb_substr($chars,rand(0, $chars_length), 1, 'UTF-8');
        }
        return $salt;
    }

    public function checkPassword($password)
    {
        return (md5($password.$this->salt) == $this->password) ? true : false;
    }
}