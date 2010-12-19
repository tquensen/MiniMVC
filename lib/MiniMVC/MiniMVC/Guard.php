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
    protected $role = null;
    protected $rights = array();
    protected $data = array();

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();

        if (isset($_SESSION['guardID'])) {
            $this->id = $_SESSION['guardID'];
        }
        if (isset($_SESSION['guardRole']) && $_SESSION['guardRole']) {
            $this->role = $_SESSION['guardRole'];
        } else {
            $this->role = $this->registry->rights->getRoleByKeyword('guest');
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
     * Check the csrfProtection of the current request
     */
    public function checkCsrfProtection($throwException = true)
    {
        $csrfData = $this->registry->settings->get('csrfToken');
        if (empty($csrfData['expected']) || empty($csrfData['submitted']) || $csrfData['expected'] != $csrfData['submitted']) {
            if ($throwException) {
                throw new Exception('invalid csrf token!', 401);
            }
            return false;
        }
        return true;
    }

    /**
     *
     * @param string|array $rights the name of the right as string ('user', 'administrator', ..) or as array of rights
     * @return bool whether the current user has the required right or not / returns true if the right is 0
     */
    public function userHasRight($rights)
    {
        if (!$rights) {
            return true;
        }
        return $this->checkRights($rights);
    }

    protected function checkRights($rights, $and = true)
    {
        if ($and) {
            foreach ((array)$rights as $right) {
                if (is_array($right)) {
                    if (!$this->checkRights($right, !$and)) {
                        return false;
                    }
                }
                if (!in_array($right, $this->rights)) {
                    return false;
                }
            }
            return true;
        } else {
            foreach ((array)$rights as $right) {
                if (is_array($right)) {
                    if ($this->checkRights($right, !$and)) {
                        return true;
                    }
                }
                if (in_array($right, $this->rights)) {
                    return true;
                }
            }
            return false;
        }
    }

}