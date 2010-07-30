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
        $b = BlubberTable::getInstance();
        $c = BlubbCommentsTable::getInstance();

        $sql  = $this->_select('u').$b->_select('b', true).$c->_select('c', true);
        $sql .= $this->_from('u').$b->_join('b', 'u.id = b.user_id').$c->_join('c', 'u.id = c.user_id');
        if ($condition) $sql .= ' WHERE '.$condition;
        if ($limit || $offset) {
            $sql .= ($condition ? ' AND ' : ' WHERE ') . $this->_in('u.id', $this->_getIdentifiers('u', $condition, $order, $limit, $offset));
        }
        if ($order) $sql .= ' ORDER BY '.$order;

        return $this->buildAll(
                $this->db->query($sql),
                array('u' => $this, 'b' => $b, 'c' => $c),
                array(array('u', 'b'), array('u', 'c'))
        );
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
