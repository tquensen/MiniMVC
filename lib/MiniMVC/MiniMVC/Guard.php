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
    protected $persistent = true;

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
     * @param bool $persistent whether to save the userdata in the eseein (true, default) or only for the current request
     */
    public function setUser($id = null, $role = 'guest', $persistent = true)
    {
        $this->persistent = (bool) $persistent;
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
        if ($this->persistent) {
            $_SESSION['guardID'] = $id;
        }
    }

    /**
     *
     * @param string $role the name of the current user's role
     */
    public function setRole($role)
    {
        $this->role = $role;
        if ($this->persistent) {
            $_SESSION['guardRole'] = $role;
        }
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
     * @return integer the current user rights
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
        if ($this->persistent) {
            $_SESSION['guardData'] = $this->data;
        }
    }

    /**
     * Check the csrfProtection of the current request
     */
    public function checkCsrfProtection($throwException = true)
    {
        $token = isset($_POST['CSRF_TOKEN']) ? $_POST['CSRF_TOKEN'] : null;
        $value = isset($_SESSION['_csrf_token'][$token]) ? $_SESSION['_csrf_token'][$token] : null;
        unset($_SESSION['_csrf_token'][$token]);

        if (!$token || !$value) {
            if ($throwException) {
                throw new Exception('invalid csrf token: '.(!$token ? ' (none)' : $token), 401);
            }
            return false;
        }
        return true;
    }

    public function generateCsrfToken()
    {
        $token = md5(time() . rand(1000, 9999));
        $_SESSION['_csrf_token'][$token] = true;
        return $token;
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
        return $this->registry->rights->roleHasRight($this->role, $rights);
    }
}