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
    protected $authToken = null;
    protected $persistent = true;
    protected $authenticatedRequest = false;

    public function __construct()
    {
        $this->registry = MiniMVC_Registry::getInstance();

        $event = new sfEvent($this, 'guard.processLogin');
        $this->registry->events->notifyUntil($event);

        if (!$event->isProcessed()) {
            if (isset($_SESSION['guardID'])) {
                $this->id = $_SESSION['guardID'];
            }
            if (isset($_SESSION['guardAuthToken'])) {
                $this->authToken = $_SESSION['guardAuthToken'];
            }
            if (isset($_SESSION['guardRole']) && $_SESSION['guardRole']) {
                $this->role = $_SESSION['guardRole'];
            }
        }

        if (isset($_SESSION['guardData'])) {
            $this->data = $_SESSION['guardData'];
        }

        if (!$this->role) {
            $this->role = $this->registry->rights->getRoleByKeyword('guest');
        }
        $this->rights = $this->registry->rights->getRoleRights($this->role);
    }

    /**
     *
     * @param integer $id the unique id of the current user
     * @param string $role the name of the current user's role
     * @param bool $persistent whether to save the userdata in the session (true) or only for the current request
     */
    public function setUser($id = null, $role = 'guest', $persistent = null)
    {
        if ($persistent !== null) {
            $this->persistent = (bool) $persistent;
        }
        $this->setId($id);
        $this->setRole($role);
        $this->clearData();
    }

    /**
     * check or set whether this is an authenticated request or not
     * @param bool|null $authenticated null to return the request status, true or false to set the request status
     * @return bool whether this is an authenticated request or not
     */
    public function isAuthenticatedRequest($authenticated = null)
    {
        if ($authenticated !== null) {
            $this->authenticatedRequest = (bool) $authenticated;
        }
        return $this->authenticatedRequest;
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
     * @param string $authToken the unique authToken of the current user
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;
        if ($this->persistent) {
            $_SESSION['guardAuthToken'] = $authToken;
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
     * @return string the current user's unique auth token
     */
    public function getAuthToken()
    {
        return $this->authToken;
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

    public function generateFormToken()
    {
        $token = md5(time().rand(10000, 99999));
        $_SESSION['formToken'][$token] = true;
        return $token;
    }

    public function checkFormToken($token = null)
    {
        if ($token === null) {
            $token = isset($_REQUEST['form_token']) ? $_REQUEST['form_token'] : null;
        }
        if ($token && !empty($_SESSION['formToken'][$token])) {
            unset($_SESSION['formToken'][$token]);
            return true;
        }
        return false;
    }

    protected function persistData()
    {
        if ($this->persistent) {
            $_SESSION['guardData'] = $this->data;
        }
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