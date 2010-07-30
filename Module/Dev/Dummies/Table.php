<?php

/**
 * @method {name} getOne()
 * @method {name} getOneBy()
 * @method {name} loadOne()
 * @method {name} loadOneBy()
 * @method {name} create()
 */
class {name}Table extends MiniMVC_Table
{

	protected $_table = '{table}';
    protected $_model = '{name}';

	protected $_columns = array('id');
    protected $_relations = array();
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;




    /**
     * Created the table for this model
     */
    public function install()
    {

    }

    /**
     * Deletes the table for this model
     */
    public function uninstall()
    {

    }

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
