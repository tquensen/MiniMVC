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
        return $this->{$this->_table->getIdentifier()};
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
            $identifier = isset($arguments[0]) ? $arguments[0] : null;
            $load = isset($arguments[1]) ? $arguments[1] : false;
            if ($identifier === true) {
                if (isset($this->_relations[$relation])) {
                    return $this->_relations[$relation];
                }
            } elseif ($identifier === null) {
                if (isset($this->_relations[$relation]) && count($this->_relations[$relation])) {
                    return reset($this->_relations[$relation]);
                }
            } else {
                if (isset($this->_relations[$relation][$identifier])) {
                    return $this->_relations[$relation][$identifier];
                }
            }
            if (!$load || !$data = $this->getTable()->getRelation($relation)) {
                return ($identifier === true) ? array() : null;
            }

            $info = $this->getTable()->getRelation($relation);
            $table = call_user_func($info[0] . 'Table::getInstance');
            $loadArguments = array();
            if ($identifier === null) {
                $loadArguments[2] = 1;
            } elseif ($identifier !== true) {
                $loadArguments[0] = $table->getIdentifier() . ' = ?';
                $loadArguments[2] = array($identifier);
            }

            $entries = $this->__call('load'.substr($name, 3), $loadArguments);
            return $identifier === true ? $entries : reset($entries);
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
            $relation = strtolower(substr($name, 4));
            $condition = isset($arguments[0]) ? $arguments[0] : null;
            $values = isset($arguments[0]) ? $arguments[0] : array();
            $order = isset($arguments[1]) ? $arguments[1] : null;
            $limit = isset($arguments[2]) ? $arguments[2] : null;
            $offset = isset($arguments[3]) ? $arguments[3] : null;

            if (!$data = $this->getTable()->getRelation($relation)) {
                return null;
            }
            if (is_string($values)) {
                $values = array($values);
            }
            array_unshift($values, $this->{$data[1]});
            $where = $data[2].' = ?' . ($condition ? ' AND '.$condition : '');
            $tableName = $data[0].'Table';
            $table = call_user_func($tableName . '::getInstance');

            $entries = $table->load($where, $values, $order, $limit, $offset);

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