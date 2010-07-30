<?php
/**
 * @method BlubbUser get()
 * @method BlubbUser getOneBy()
 * @method BlubbUser load()
 * @method BlubbUser loadOneBy()
 * @method BlubbUser create()
 * @method BlubbUser get()
 */
class BlubbUserTable extends MiniMVC_Table
{

	protected $table = 'blubb_user';
    protected $entryClass = 'BlubbUser';

	protected $columns = array('id', 'username');
    protected $relations = array('comments' => array('BlubbComments', 'id', 'user_id'), 'blubber' => array('Blubber', 'id', 'user_id'));
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    protected static $_instance = null;


    public function loadWithRelations($condition = null, $order = null, $limit = null, $offset = null)
	{
        return $this->query('u')->select('b')->select('c')
                ->join('u','blubber','b')
                ->join('u','comments','c')
                ->where($condition)
                ->orderBy($order)
                ->limit($limit, $offset, true)
                ->build();
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
