<?php
/**
 * @method Blubber get()
 * @method Blubber getOneBy()
 * @method Blubber load()
 * @method Blubber loadOneBy()
 * @method Blubber create()
 * @method Blubber get()
 */
class BlubberTable extends MiniMVC_Table
{

	protected $table = 'blubber';
    protected $entryClass = 'Blubber';

	protected $columns = array('id', 'name', 'slug', 'user_id');
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    protected static $_instance = null;
  

    public function loadWithNumComments($condition, $order = null, $limit = null, $offset = null)
	{
        $c = BlubbCommentsTable::getInstance();
        $sql  = $this->_select('a').', count(b.id) a__comments_count';
        $sql .= $this->_from('a').$c->_join('b', 'a.id = b.blubb_id');
        if ($condition) $sql .= ' WHERE '.$condition;
        $sql .= ' GROUP BY a.id ';
        if ($order) $sql .= ' ORDER BY '.$order;
        if ($limit || $offset) $sql .= ' LIMIT '.intval($offset).', '.intval($limit).' ';

        return  $this->buildAll($this->db->query($sql), 'a');
	}

    /**
     * @param string $condition the where condition("id = 1", "a.username LIKE 'foo%'")
     * @param string $order an order by clause (id ASC, foo DESC)
     * @param int $limit
     * @param int $offset
     * @return array
     */
	public function loadWithComments($condition, $order = null, $limit = null, $offset = null)
	{
        $userTable = BlubbUserTable::getInstance();
        $commentsTable = BlubbCommentsTable::getInstance();

		$sql  = $this->_select('a').$userTable->_select('u', true).$commentsTable->_select('c', true).$userTable->_select('cu', true);
        $sql .= $this->_from('a').$userTable->_join('u', 'a.user_id = u.id').$commentsTable->_join('c', 'a.id = c.blubb_id').$userTable->_join('cu', 'c.user_id = cu.id');
        if ($condition)  $sql .= ' WHERE '.$condition.' ';
        if ($limit || $offset) {
            $sql .= ($condition ? ' AND ' : ' WHERE ') . $this->_in('a.id', $this->_getIdentifiers('a', $condition, $order, $limit, $offset));
        }
        if ($order) $sql .= ' ORDER BY '.$order;

        return $this->buildAll(
                $this->db->query($sql),
                array('a' => $this, 'u' => $userTable, 'c' => $commentsTable, 'cu' => $userTable),
                array(array('a', 'c'), array('a', 'u'), array('c', 'cu'))
        );
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
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new BlubberTable;
        }
        return self::$_instance;
    }

    
}
