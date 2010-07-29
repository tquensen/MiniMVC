<?php

class BlubberTable extends MiniMVC_Table
{

	protected $table = 'blubber';
    protected $entryClass = 'Blubber';

	protected $columns = array('id', 'name', 'slug', 'user_id');
	protected $primary = 'id';
	protected $isAutoIncrement = true;

    protected static $_instance = null;

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

    public function loadWithNumComments($condition, $order = null, $limit = null, $offset = null)
	{
        $sql  = $this->_select('a').', count(b.id) a__comments_count';
        $sql .= ' FROM '.$this->table.' a LEFT JOIN blubb_comments b ON a.id = b.blubb_id';
        if ($condition) $sql .= ' WHERE '.$condition;
        $sql .= ' GROUP BY a.id ';
        if ($order) $sql .= ' ORDER BY '.$order;
        if ($limit || $offset) $sql .= ' LIMIT '.intval($offset).', '.intval($limit).' ';

		$result = $this->db->query($sql);

        $entries = array();
        while($row = $result->fetch_assoc()) {
            if (!isset($entries[$row['a__'.$this->primary]])) {
                $entries[$row['a__'.$this->primary]] = $this->_buildEntry($row, 'a');
            }
        }
		return $entries;
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
        $ids = null;
        if ($limit || $offset) {
            $preSelect = 'SELECT id FROM blubber a ';
            if ($condition) $preSelect .= ' WHERE '.$condition;
            if ($order) $preSelect .= ' ORDER BY '.$order;
            if ($limit || $offset) $preSelect .= ' LIMIT '.intval($offset).', '.intval($limit).' ';
            $ids = array();

            $preResult = $this->db->query($preSelect);
            while($preRow = $preResult->fetch_assoc()) {
                $ids[] = $preRow['id'];
            }
        }

        $userTable = BlubbUserTable::getInstance();
        $commentsTable = BlubbCommentsTable::getInstance();

		$sql  = $this->_select('a').$userTable->_select('u', false).$commentsTable->_select('c', false).$userTable->_select('cu', false);
        $sql .= ' FROM '.$this->table.' a LEFT JOIN blubb_user u ON a.user_id = u.id LEFT JOIN blubb_comments c ON a.id = c.blubb_id LEFT JOIN blubb_user cu ON c.user_id = cu.id';
        if ($condition) {
            $sql .= ' WHERE '.$condition.' ';
            if ($ids) {
                $sql .= ' AND a.id IN ('.implode(',',$ids).') ';
            }
        } elseif ($ids) {
            $sql .= ' WHERE a.id IN ('.implode(',',$ids).') ';
        }
        if ($order) $sql .= ' ORDER BY '.$order;

		$result = $this->db->query($sql);

        echo $sql."\n<br />\n";

        $start = microtime(true);
		$result = $this->db->query($sql);
        echo '<br />TIME QUERY: '.number_format(microtime(true)-$start, 6, ',','').'s';

        $entries = array();
        $comments = array();
        $user = array();

        $start = microtime(true);
        while($row = $result->fetch_assoc()) {
            if (!isset($entries[$row['a__id']])) {
                $entries[$row['a__id']] = $this->_buildEntry($row, 'a');
            }
            if (!isset($comments[$row['c__id']])) {
                $comments[$row['c__id']] = $commentsTable->_buildEntry($row, 'c');
            }
            if (!isset($user[$row['u__id']])) {
                $user[$row['u__id']] = $userTable->_buildEntry($row, 'u');
            }
            if (!isset($user[$row['cu__id']])) {
                $user[$row['cu__id']] = $userTable->_buildEntry($row, 'cu');
            }
            if ($entries[$row['a__id']]) {
                if ($comments[$row['c__id']]) {
                    $entries[$row['a__id']]->setComment($comments[$row['c__id']], $row['c__id'], false);
                }
                if ($user[$row['u__id']]) {
                    $entries[$row['a__id']]->setUser($user[$row['u__id']], 0, false);
                }
            }
            if ($comments[$row['c__id']] && $user[$row['cu__id']]) {
                $comments[$row['c__id']]->setUser($user[$row['cu__id']], 0, false);
            }
        }
        echo '<br />TIME BUILD: '.number_format(microtime(true)-$start, 6, ',','').'s';
		return $entries;
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
