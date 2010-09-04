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
    public function install($installedVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                $sql = 'CREATE TABLE `{table}` (
					  `id` int(11) NOT NULL auto_increment,
					  PRIMARY KEY  (`id`)
					) ENGINE=INNODB DEFAULT CHARSET=utf8';

                $this->_db->query($sql);
            case 1:
        }
        return true;
    }

    /**
     * Deletes the table for this model
     */
    public function uninstall($installedVersion = 'max')
    {

        SWITCH ($installed_version) {
            case 'max':
            case 1:
                $sql = 'DROP TABLE `{table}`';
                $this->_db->query($sql);
        }
        return true;
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
