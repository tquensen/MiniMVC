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
    protected $_relations = array('Comments' => array('BlubbComments', 'id', 'user_id'), 'Blubber' => array('Blubber', 'id', 'user_id'), 'Groups' => array('Group', 'user_id', 'group_id', 'group_user'));
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;


    public function loadWithRelations($condition = null, $values = array(), $order = null, $limit = null, $offset = null)
	{
        return $this->query('u')->select('b, c, g')
                ->join('u.Blubber','b')
                ->join('u.Comments','c')
                ->join('u.Groups','g')
                ->where($condition)
                ->orderBy($order)
                ->limit($limit, $offset, true)
                ->build($values);
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
