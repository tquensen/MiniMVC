<?php

/**
 * @method {name}Collection loadAll() loadAll($order = null)
 * @method {name}Collection load() load($condition = null, $value = null, $order = null, $limit = null, $offset = null, $returnAs = 'object')
 * @method {name}Collection loadWithRelations() loadWithRelations($relations = array(), $condition = null, $value = null, $order = null, $limit = null, $offset = null, $needPreQuery = null, $returnAs = 'object')
 * @method {name} loadOne() loadOne($id)
 * @method {name} loadOneBy() loadOneBy($condition, $value = null, $order = null, $offset = 0)
 * @method {name} loadOneWithRelations() loadOneWithRelations($id, $relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = false)
 * @method {name} loadOneWithRelationsBy() loadOneWithRelationsBy($relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = true)
 * @method {name} create() create($data = array())
 * @method {name}Collection getCollection()
 */
abstract class {name}TableBase extends {tableClass}
{

	protected $_table = '{table}';
    protected $_model = '{name}';

	protected $_columns = array({columns_list});
    protected $_relations = array({relations_list});
	protected $_identifier = '{identifier}';
	protected $_isAutoIncrement = {auto_increment};
    
    /**
     *
     * @param string $connection the database connection to use (null for the default connection)
     * @return {name}Table
     */
    public static function getInstance($connection = null)
    {
        return new {name}Table($connection);
    }
}
