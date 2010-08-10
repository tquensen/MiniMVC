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
        if ($rights = $this->registry->settings->get('roles/'.$this->role.'/rights')) {
            $this->rights = (int) $rights;
        }
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
        $this->rights = (int)$this->registry->settings->get('roles/'.$this->role.'/rights');
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
        $_SESSION['guardData'] = array();
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
        $_SESSION['guardData'] = $this->data;
        return true;
    }

}