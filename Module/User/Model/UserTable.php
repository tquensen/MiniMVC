<?php

/**
 * @method User getOne()
 * @method User getOneBy()
 * @method User loadOne()
 * @method User loadOneBy()
 * @method User create()
 */
class UserTable extends MiniMVC_Table
{

	protected $_table = 'user';
    protected $_model = 'User';

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
