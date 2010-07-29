<?php
class MiniMVC_Model
{
	protected $_properties = array();
    protected $_databaseProperties = array();
    protected $_relations = array();
	protected $_table = null;
	protected $_isNew = true;

	public function __construct($table = null)
	{
        if ($table) {
            $this->_table = $table;
        } else {
            $tableName = get_class($this).'Table';
            $this->_table = call_user_func($tableName . '::getInstance');
        }
	}

    public function getIdentifier()
    {
        $identifier = $this->_table->getIdentifier();
        return $this->$identifier;
    }

    /**
     *
     * @return MiniMVC_Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    public function isNew()
    {
        return empty($this->_databaseProperties);
    }

    public function getDatabaseProperty($key)
    {
        return isset($this->_databaseProperties[$key]) ? $this->_databaseProperties[$key] : null;
    }

    public function setDatabaseProperty($key, $value)
    {
        $this->_databaseProperties[$key] = $value;
    }

    public function clearDatabaseProperties()
    {
        $this->_databaseProperties = array();
    }

	public function __get($key)
	{
        return isset($this->_properties[$key]) ? $this->_properties[$key] : null;
	}

	public function __set($key, $value)
	{
		$this->_properties[$key] = $value;
	}

    public function __isset($key)
	{
        return isset($this->_properties[$key]);
	}

    public function __unset($key)
	{
        if (isset($this->_properties[$key])) {
            unset($this->_properties[$key]);
        }
	}

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'get') {
            $relation = strtolower(substr($name, 3));
            $identifier = isset($arguments[0]) ? $arguments[0] : false;
            if ($identifier === true) {
                return (isset($this->_relations[$relation])) ? $this->_relations[$relation] : array();
            } elseif ($identifier === false) {
                return (isset($this->_relations[$relation]) && count($this->_relations[$relation])) ? reset($this->_relations[$relation]) : null;
            }
            return (isset($this->_relations[$relation][$identifier])) ? $this->_relations[$relation][$identifier] : null;
        }
        if (substr($name, 0, 3) == 'set') {
            $relation = strtolower(substr($name, 3));

            $update = isset($arguments[1]) ? $arguments[1] : true;
            if (isset($arguments[0]) && is_object($arguments[0])) {
                if ($update || !isset($this->_relations[$relation][$arguments[0]->getIdentifier()])) {
                    $this->_relations[$relation][$arguments[0]->getIdentifier()] = $arguments[0];
                    return true;
                }
            }
            return false;
        }
        elseif (substr($name, 0, 6) == 'delete') {
            $relation = strtolower(substr($name, 6));

            $identifier = isset($arguments[0]) ? $arguments[0] : 0;
            $realDelete = isset($arguments[1]) ? $arguments[1] : false;
            if (is_object($identifier)) {
                if (isset($this->_relations[$relation][$arguments[0]->getIdentifier()])) {
                    unset($this->_relations[$relation][$arguments[0]->getIdentifier()]);
                    if ($realDelete) {
                        $identifier->delete();
                    }
                    return true;
                }
            } else {
                if (isset($this->_relations[$relation][$identifier])) {
                    unset($this->_relations[$relation][$identifier]);
                    return true;
                }
            }
            return false;
        }
        elseif (substr($name, 0, 4) == 'load') {
            $class = substr($name, 4);
            $relation = strtolower($class);
            $condition = isset($arguments[0]) ? $arguments[0] : null;
            $order = isset($arguments[1]) ? $arguments[1] : null;
            $limit = isset($arguments[2]) ? $arguments[2] : null;
            $offset = isset($arguments[3]) ? $arguments[3] : null;
            $tableName = $class.'Table';
            $table = call_user_func($tableName . '::getInstance');
            $entries = $table->load($condition, $order, $limit, $offset);
            foreach ($entries as $entry) {
                $this->_relations[$relation][$entry->getIdentifier()] = $entry;
            }
            return $entries;
        }
        return null;
    }
    
	public function save()
	{
		return ($this->_table && method_exists($this->_table, 'save')) ? $this->_table->save($this) : false;
	}

	public function delete()
	{
		return ($this->_table && method_exists($this->_table, 'delete')) ? $this->_table->delete($this) : false;
	}

    public function __toString()
    {
        return get_class($this).'<pre>'.print_r($this->_properties, true).'</pre>';
    }

    public function preSave()
    {

    }

    public function preInsert()
    {

    }

    public function preUpdate()
    {

    }

    public function preDelete()
    {

    }

    public function postSave()
    {

    }

    public function postInsert()
    {

    }

    public function postUpdate()
    {

    }

    public function postDelete()
    {
        
    }

    public function postCreate()
    {

    }

    public function postLoad()
    {

    }
}