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
        return (isset($roleData['rights'])) ? (int) $roleData['rights'] : 0;
    }

    /**
     *
     * @param string $rights the key (name) of a right
     * @return integer the requested rights as bitmask or 0 if no right was found
     */
    public function getRights($rights)
    {
        return (int) MiniMVC_Registry::getInstance()->settings->get('rights/'.$rights.'/key');
    }

    /**
     *
     * @return integer returns a combined bitmask of all rights available (useful for super admins)
     */
    public function getAllRights()
    {
        $return = 0;
        $rights = MiniMVC_Registry::getInstance()->settings->get('rights');
        foreach ($rights as $right)
        {
            if (!isset($right['key']))
            {
                continue;
            }
            $return = $return | (int) $right['key'];
        }
        return $return;
    }
}
