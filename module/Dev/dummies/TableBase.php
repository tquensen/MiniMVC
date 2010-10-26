<?php

/**
 * @method {name} loadOne() loadOne($id)
 * @method {name} loadOneBy() loadOneBy($condition, $value = null, $order = null, $offset = 0)
 * @method {name} loadOneWithRelations() loadOneWithRelations($id, $relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = false)
 * @method {name} loadOneWithRelationsBy() loadOneWithRelationsBy($relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = true)
 * @method {name} create() create($data = array())
 */
abstract class {name}TableBase extends {tableClass}
{

	protected $_table = '{table}';
    protected $_model = '{name}';

	protected $_columns = array({columns_list});
    protected $_relations = array({relations_list});
	protected $_identifier = '{identifier}';
	protected $_isAutoIncrement = {auto_increment};

    protected static $_instance = null;
    
    /**
     *
     * @return {name}Table
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new {name}Table;
        }
        return self::$_instance;
    }
}
