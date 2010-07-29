<?php

class BlubbCommentsTable extends MiniMVC_Table
{

	protected $table = 'blubb_comments';
    protected $entryClass = 'BlubbComments';

	protected $columns = array('id', 'blubb_id', 'user_id', 'message');
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    protected static $_instance = null;

    /**
     * @method BlubbComments get()
     * @method BlubbComments getOneBy()
     * @method BlubbComments load()
     * @method BlubbComments loadOneBy()
     * @method BlubbComments create()
     * @method BlubbComments get()
     */

	public function __construct()
	{
		parent::__construct();
	}

    /**
     * @param BlubbComments $entry
     * @return BlubbComments
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
     * @return BlubbCommentsTable
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new BlubbCommentsTable;
        }
        return self::$_instance;
    }
}
