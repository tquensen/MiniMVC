<?php
class MiniMVC_Rights
{
    public function getRoleByKeyword($keyword)
    {
        foreach (MiniMVC_Registry::getInstance()->settings->roles as $key => $role)
        {
            if (isset($role['keyword']) && $role['keyword'] == $keyword)
            {
                return $key;
            }
        }
    }

    public function getRoleData($role)
    {
        return (isset(MiniMVC_Registry::getInstance()->settings->roles[$role])) ? MiniMVC_Registry::getInstance()->settings->roles[$role] : null;
    }

    public function getRoleRights($role)
    {
        $roleData = $this->getRoleData($role);
        return (isset($roleData['rights'])) ? (int) $roleData['rights'] : 0;
    }

    public function getRights($rights)
    {
        return (int) (isset(MiniMVC_Registry::getInstance()->settings->rights[$rights]['key'])) ? MiniMVC_Registry::getInstance()->settings->rights[$rights]['key'] : 0;
    }

    public function getAllRights()
    {
        $return = 0;
        $rights = MiniMVC_Registry::getInstance()->settings->rights;
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
