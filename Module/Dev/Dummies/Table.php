<?php

class {name}Table extends MiniMVC_Table
{

	protected $table = '{table}';
    protected $entryClass = '{name}';

	protected $columns = array('id');
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    /**
     * @method {name} get()
     * @method {name} getOneBy()
     * @method {name} load()
     * @method {name} loadOneBy()
     * @method {name} create()
     * @method {name} get()
     */

	public function __construct()
	{
		parent::__construct();
	}

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
    public static function get()
    {
        return new {name}Table;
    }
}
