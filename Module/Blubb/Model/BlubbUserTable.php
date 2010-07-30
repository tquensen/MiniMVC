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

	protected $_table = 'blubb_user';
    protected $_model = 'BlubbUser';

	protected $_columns = array('id', 'username');
    protected $_relations = array('comments' => array('BlubbComments', 'id', 'user_id'), 'blubber' => array('Blubber', 'id', 'user_id'));
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

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
