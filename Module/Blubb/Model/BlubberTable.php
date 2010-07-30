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
        return $this->query('a')
                ->select('count(b.id) a__comments_count')
                ->join(BlubbCommentsTable::getInstance(), 'b', 'a', 'a.id = b.blubb_id')
                ->where($condition)
                ->orderBy($order)
                ->groupBy('a.id')
                ->limit($limit, $offset)
                ->build();
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
        return $this->query('a')
                ->select('u')->select('c')->select('cu')
                ->join(BlubbUserTable::getInstance(), 'u', 'a', 'a.user_id = u.id')
                ->join(BlubbCommentsTable::getInstance(), 'c', 'a', 'a.id = c.blubb_id')
                ->join(BlubbUserTable::getInstance(), 'cu', 'c', 'c.user_id = cu.id')
                ->where($condition)
                ->orderBy($order)
                ->limit($limit, $offset, true)
                ->build();
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
