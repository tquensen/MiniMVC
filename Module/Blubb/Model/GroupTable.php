<?php

/**
 * @method Group getOne()
 * @method Group getOneBy()
 * @method Group loadOne($id)
 * @method Group loadOneBy()
 * @method Group create()
 */
class GroupTable extends MiniMVC_Table
{

	protected $_table = 'blubb_group';
    protected $_model = 'Group';

	protected $_columns = array('id', 'name');
    protected $_relations = array('user' => array('BlubbUser', 'group_id', 'user_id', 'group_user'));
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
     * @return GroupTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new GroupTable;
        }
        return self::$_instance;
    }
}
