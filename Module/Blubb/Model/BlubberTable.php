<?php

class BlubberTable extends MiniMVC_Table
{

	protected $table = 'blubber';
    protected $entryClass = 'Blubber';

	protected $columns = array('id');
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    /**
     * @method Blubber get()
     * @method Blubber getOneBy()
     * @method Blubber load()
     * @method Blubber loadOneBy()
     * @method Blubber create()
     * @method Blubber get()
     */

	public function __construct()
	{
		parent::__construct();
	}

    /**
     * @param Blubber $entry
     * @return Blubber
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
     * @return BlubberTable
     */
    public static function get()
    {
        return new BlubberTable;
    }
}
