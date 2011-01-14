<?php

/**
 * @method UserCollection loadAll() loadAll($order = null)
 * @method UserCollection load() load($condition = null, $value = null, $order = null, $limit = null, $offset = null, $returnAs = 'object')
 * @method UserCollection loadWithRelations() loadWithRelations($relations = array(), $condition = null, $value = null, $order = null, $limit = null, $offset = null, $needPreQuery = null, $returnAs = 'object')
 * @method User loadOne() loadOne($id)
 * @method User loadOneBy() loadOneBy($condition, $value = null, $order = null, $offset = 0)
 * @method User loadOneWithRelations() loadOneWithRelations($id, $relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = false)
 * @method User loadOneWithRelationsBy() loadOneWithRelationsBy($relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = true)
 * @method User create() create($data = array())
 * @method UserCollection getCollection()
 */
abstract class UserTableBase extends MiniMVC_Table
{

	protected $_table = 'user';
    protected $_model = 'User';

	protected $_columns = array('id', 'slug', 'name', 'email', 'password', 'salt', 'auth_token', 'role', 'created_at', 'updated_at');
    protected $_relations = array();
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;
    
    /**
     *
     * @return UserTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new UserTable;
        }
        return self::$_instance;
    }
}
