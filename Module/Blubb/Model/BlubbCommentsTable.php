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
        $userTable = BlubbUserTable::getInstance();

        $sql  = $this->_select('c');
        $sql .= $userTable->_select('u', false);
        $sql .= ' FROM '.$this->table.' c LEFT JOIN blubb_user u ON c.user_id = u.id';
        if ($condition) $sql .= ' WHERE '.$condition;
        if ($order) $sql .= ' ORDER BY '.$order;
        if ($limit || $offset) $sql .= ' LIMIT '.intval($offset).', '.intval($limit).' ';

		$result = $this->db->query($sql);

        return $this->buildAll($result, array('c' => $this, 'u' => $userTable), array(array('u', 'c'), array('c', 'u')));
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
