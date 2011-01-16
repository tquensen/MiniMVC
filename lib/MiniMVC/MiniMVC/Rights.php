<?php
/**
 * MiniMVC_Rights is responsible for the role/right management
 */
class MiniMVC_Rights
{
    /**
     *
     * @param string $keyword a keyword for a role
     * @return string returns the key (name) of the role
     */
    public function getRoleByKeyword($keyword)
    {
        foreach (MiniMVC_Registry::getInstance()->settings->get('roles', array()) as $key => $role)
        {
            if (isset($role['keyword']) && $role['keyword'] == $keyword)
            {
                return $key;
            }
        }
    }

    /**
     *
     * @param string $role the key (name) of a role
     * @return mixed returns an array wiht data of the requested role or null if no role was found
     */
    public function getRoleData($role)
    {
        return MiniMVC_Registry::getInstance()->settings->get('roles/'.$role);
    }

    /**
     *
     * @param string $role the key (name) of a role
     * @return integer the rights of the requested role as bitmask or 0 if no role was found
     */
    public function getRoleRights($role)
    {
        $roleData = $this->getRoleData($role);
        return (isset($roleData['rights'])) ? (array) $roleData['rights'] : array();
    }

    /**
     *
     * @param string $role the name of a role ('guest', 'user')
     * @param string|array $rights the name of the right as string ('user', 'administrator', ..) or as array of rights
     * @return bool whether the given role has the required right or not / returns true if the right is 0
     */
    public function roleHasRight($role, $rights)
    {
        if (!$rights) {
            return true;
        }
        $roleRights = $this->getRoleRights($role);
        if (!$roleRights) {
            return false;
        }
        return $this->checkRights($roleRights, $rights);
    }

    protected function checkRights($givenRights, $requiredRights, $and = true)
    {
        if ($and) {
            foreach ((array)$requiredRights as $right) {
                if (is_array($right)) {
                    if (!$this->checkRights($givenRights, $right, !$and)) {
                        return false;
                    }
                }
                if (!in_array($right, (array) $givenRights)) {
                    return false;
                }
            }
            return true;
        } else {
            foreach ((array)$requiredRights as $right) {
                if (is_array($right)) {
                    if ($this->checkRights($givenRights, $right, !$and)) {
                        return true;
                    }
                }
                if (in_array($right, (array) $givenRights)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * @deprecated
     * @param string $rights the key (name) of a right
     * @return integer the requested rights as bitmask or 0 if no right was found
     */
    public function getRights($rights)
    {
        return (array) $rights;
        //return MiniMVC_Registry::getInstance()->settings->get('rights/'.$rights.'/key');
    }

    /**
     *
     * @deprecated
     * @return integer returns a combined bitmask of all rights available (useful for super admins)
     */
    public function getAllRights()
    {
        //$rights = MiniMVC_Registry::getInstance()->settings->get('rights');
        //return array_keys($rights);
    }
}
