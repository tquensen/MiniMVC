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

	protected $table = '{table}';
    protected $entryClass = '{name}';

	protected $columns = array('id');
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    protected static $_instance = null;

    /**
     * @param {name} $entry
     * @return {name}
     */
	protected function buildEntry($entry)
	{
		/*
		 $entry->additionalData = $entry->id.'Something';
		 */
		return $entry;
	}

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
