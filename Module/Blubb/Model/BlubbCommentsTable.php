<?php
/**
 * @method BlubbComments get()
 * @method BlubbComments getOneBy()
 * @method BlubbComments load()
 * @method BlubbComments loadOneBy()
 * @method BlubbComments create()
 */
class BlubbCommentsTable extends MiniMVC_Table
{

	protected $table = 'blubb_comments';
    protected $entryClass = 'BlubbComments';

	protected $columns = array('id', 'blubb_id', 'user_id', 'message');
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    protected static $_instance = null;

    public function loadWithUser($condition = null, $order = null, $limit = null, $offset = null)
	{
        return $this->query('c')->select('u')->join(BlubbUserTable::getInstance(), 'u', 'c', 'u.id = c.user_id')
                ->where($condition)->orderBy($order)->limit($limit, $offset)->build();
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
