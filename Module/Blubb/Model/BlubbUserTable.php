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
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    protected static $_instance = null;


    public function loadWithRelations($condition = null, $order = null, $limit = null, $offset = null)
	{
        //get right limit/offset
        $ids = null;
        if ($limit || $offset) {
            $preSelect = 'SELECT id FROM blubb_user u ';
            if ($condition) $preSelect .= ' WHERE '.$condition;
            if ($order) $preSelect .= ' ORDER BY '.$order;
            if ($limit || $offset) $preSelect .= ' LIMIT '.intval($offset).', '.intval($limit).' ';
            $ids = array();

            $preResult = $this->db->query($preSelect);
            while($preRow = $preResult->fetch_assoc()) {
                $ids[] = $preRow['id'];
            }
        }


        $blubberTable = BlubberTable::getInstance();
        $commentsTable = BlubbCommentsTable::getInstance();

        $sql  = $this->_select('u');
        $sql .= $blubberTable->_select('b', false);
        $sql .= $commentsTable->_select('c', false);
        $sql .= ' FROM '.$this->table.' u LEFT JOIN blubber b ON u.id = b.user_id LEFT JOIN blubb_comments c ON u.id = c.user_id';
        if ($condition) $sql .= ' WHERE '.$condition;
        if ($ids) $sql .= ($condition ? ' AND ' : ' WHERE ') . 'u.id IN ('.implode(',',$ids).') ';
        if ($order) $sql .= ' ORDER BY '.$order;

		$result = $this->db->query($sql);

        return $this->buildAll($result, array('u' => $this, 'b' => $blubberTable, 'c' => $commentsTable), array(array('u', 'b'), array('u', 'c')));
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
