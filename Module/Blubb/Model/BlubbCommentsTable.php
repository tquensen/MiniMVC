<?php
/**
 * @method BlubbComments get()
 * @method BlubbComments getOneBy()
 * @method BlubbComments loadOne()
 * @method BlubbComments loadOneBy()
 * @method BlubbComments create()
 */
class BlubbCommentsTable extends MiniMVC_Table
{

	protected $_table = 'blubb_comments';
    protected $_model = 'BlubbComments';

	protected $_columns = array('id', 'blubb_id', 'user_id', 'message');
    protected $_relations = array('user' => array('BlubbUser', 'user_id', 'id'), 'blubb' => array('Blubber', 'blubb_id', 'id'));
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected static $_instance = null;

    public function loadWithUser($condition = null, $values = array(), $order = null, $limit = null, $offset = null)
	{
        return $this->query('c')->select('u')->join('c','user','u')
                ->where($condition)->orderBy($order)->limit($limit, $offset)->build($values);
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
