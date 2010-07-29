<?php

class BlubbUserTable extends MiniMVC_Table
{

	protected $table = 'blubb_user';
    protected $entryClass = 'BlubbUser';

	protected $columns = array('id', 'username');
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    protected static $_instance = null;

    /**
     * @method BlubbUser get()
     * @method BlubbUser getOneBy()
     * @method BlubbUser load()
     * @method BlubbUser loadOneBy()
     * @method BlubbUser create()
     * @method BlubbUser get()
     */

	public function __construct()
	{
		parent::__construct();
	}

    /**
     * @param BlubbUser $entry
     * @return BlubbUser
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
     * @return BlubbUserTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new BlubbUserTable;
        }
        return self::$_instance;
    }

    
}
