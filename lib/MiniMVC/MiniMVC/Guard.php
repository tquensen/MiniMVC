<?php

/**
 * MiniMVC_Guard is responsible for the current user session
 */
class MiniMVC_Guard
{
    /**
     *
     * @var MiniMVC_Registry
     */
    protected $registry = null;
    protected $id = null;
    protected $role = 'guest';
    protected $rights = 0;
    protected $data = array();

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();

        if (isset($_SESSION['guardID'])) {
            $this->id = $_SESSION['guardID'];
        }
        if (isset($_SESSION['guardRole']) && $_SESSION['guardRole']) {
            $this->role = $_SESSION['guardRole'];
        }
        if (isset($_SESSION['guardData'])) {
            $this->data = $_SESSION['guardData'];
        }

        $this->rights = $this->registry->rights->getRoleRights($this->role);
    }

    /**
     *
     * @param integer $id the unique id of the current user
     * @param string $role the name of the current user's role
     */
    public function setUser($id = null, $role = 'guest')
    {
        $this->setId($id);
        $this->setRole($role);
        $this->clearData();
    }

    /**
     *
     * @param integer $id the unique id of the current user
     */
    public function setId($id)
    {
        $this->id = $id;
        $_SESSION['guardID'] = $id;
    }

    /**
     *
     * @param string $role the name of the current user's role
     */
    public function setRole($role)
    {
        $this->role = $role;
        $_SESSION['guardRole'] = $role;
        $this->rights = $this->registry->rights->getRoleRights($this->role);
    }

    /**
     *
     * @return integer the current user's unique id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return string the current user's role name
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     *
     * @return integer the current user right bitmask
     */
    public function getRights()
    {
        return $this->rights;
    }

    public function clearData()
    {
        $this->data = array();
        $this->persistData();
    }

    /**
     *
     * @param string $key the unique data key
     * @return mixed the data for the given key or null if no data was found
     */
    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }
        return (isset($this->data[$key])) ? $this->data[$key] : null;
    }

    /**
     *
     * @param string $key the unique data key
     * @param mixed $value the value to store
     * @return bool returns always true
     */
    public function __set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->__set($k, $v);
            }
            return true;
        }
        $this->data[$key] = $value;
        $this->persistData();
        return true;
    }

    protected function persistData()
    {
        $_SESSION['guardData'] = $this->data;
    }

    /**
     *
     * @param string|int $right the right to check, either as the name of the right as string ('user', 'administrator', ..) or as the key as int (2, 1024, ..)
     * @return bool whether the current user has the required right or not / returns true if the right is 0
     */
    public function userHasRight($right)
    {
        if (!$right) {
            return true;
        }
        if (!is_int($right) && !is_numeric($right)) {
            $right = $this->registry->rights->getRights($right);
        }
        return (bool) ((int) $this->rights & (int) $right);
    }

    /**
     *
     * @param mixed $message the message to store
     * @param string $type the message type, (notice, warning, error, ...)
     */
    public function setMessage($message, $type = 'notice')
    {
        $this->data['messages'][$type][] = $message;
        $this->persistData();
    }

    /**
     *
     * @param string|bool $type the messagetype to check or false to check any type
     * @return bool
     */
    public function hasMessages($type = 'notice')
    {
        if (!$type) {
            return !empty($this->data['messages']);
        }
        return !empty($this->data['messages'][$type]);
    }

    /**
     *
     * @param string|bool $type the messagetype to return or false to return any types
     * @param bool $remove whether to remove the messages after returning (default true)
     * @return array an array of all messages of the requested type or an array of all types
     */
    public function getMessages($type = 'notice', $remove = true)
    {
        $messages = array();
        if (!$type) {
            $messages = isset($this->data['messages']) ? $this->data['messages'] : array();
            unset($this->data['messages']);          
        } else {
            $messages = isset($this->data['messages'][$type]) ? $this->data['messages'][$type] : array();
            unset($this->data['messages'][$type]);
        }
        $this->persistData();
        return $messages;
    }

}