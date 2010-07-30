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

	protected $_table = 'blubber';
    protected $_model = 'Blubber';

	protected $_columns = array('id', 'name', 'slug', 'user_id');
    protected $_relations = array('user' => array('BlubbUser', 'user_id', 'id'), 'comments' => array('BlubbComments', 'id', 'blubb_id'));
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;
  

    public function loadWithNumComments($condition, $order = null, $limit = null, $offset = null)
	{
        return $this->query('a')
                ->select('count(b.id) a__comments_count')
                ->join('a', 'comments', 'b')
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
                ->join('a', 'user', 'u')
                ->join('a', 'comments', 'c')
                ->join('c', 'user', 'cu')
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
