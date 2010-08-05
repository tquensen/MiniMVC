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
            $identifier = isset($arguments[0]) ? $arguments[0] : true;
            $relationInfo = $this->_table->getRelation($relation);
            if ($identifier === true) {
                if (isset($this->_relations[$relation])) {
                    return (isset($relationInfo[3]) && $relationInfo[3] === true) ? reset($this->_relations[$relation]) : $this->_relations[$relation];
                }
            } else {
                if (isset($this->_relations[$relation]['id_'.$identifier])) {
                    return $this->_relations[$relation]['id_'.$identifier];
                }
            }
            return ($identifier === true && (!isset($relationInfo[3]) || $relationInfo[3] !== true)) ? array() : null;
        }
        if (substr($name, 0, 3) == 'set') {
            $relation = strtolower(substr($name, 3));

            $update = isset($arguments[1]) ? $arguments[1] : true;
            if (isset($arguments[0]) && is_object($arguments[0])) {
                if (!$arguments[0]->getIdentifier()) {
                    $this->_relations[$relation][] = $arguments[0];
                    return true;
                }
                if ($update || !isset($this->_relations[$relation]['id_'.$arguments[0]->getIdentifier()])) {
                    $this->_relations[$relation]['id_'.$arguments[0]->getIdentifier()] = $arguments[0];
                    return true;
                }
            }
            return false;
        }
        elseif (substr($name, 0, 6) == 'delete') {
            $relation = strtolower(substr($name, 6));

            $identifier = isset($arguments[0]) ? $arguments[0] : true;
            $realDelete = isset($arguments[1]) ? $arguments[1] : false;
            $realDeleteLoad = isset($arguments[2]) ? $arguments[2] : false;
            $realDeleteCleanRef = isset($arguments[3]) ? $arguments[3] : false;
            if (is_object($identifier)) {
                if (isset($this->_relations[$relation]['id_'.$arguments[0]->getIdentifier()])) {
                    unset($this->_relations[$relation]['id_'.$arguments[0]->getIdentifier()]);
                    if ($realDelete) {
                        $identifier->delete();
                    }
                    return true;
                }
            } else {
                if (!$data = $this->getTable()->getRelation($relation)) {
                    return null;
                }
                if ($identifier === true) {
                    if ($realDelete === true) {
                        if ($realDeleteLoad === true) {
                            $tableName = $data[0].'Table';
                            $table = call_user_func($tableName . '::getInstance');
                            if (isset($data[3]) && $data[3] !== true) {
                                MiniMVC_Query::create()->delete('b')->from($this->_table->getModelName(), 'a')->join('a', $relation, 'b')->where('a.'.$this->_table->getIdentifier().' = ? AND b.'.$table->getIdentifier(). ' IS NOT NULL')->execute($this->getIdentifier());
                                if ($realDeleteCleanRef) {
                                    $table->cleanRefTables();
                                }
                            } else {
                                $table->deleteBy($data[2] . ' = ?', $this->{$data[1]}, $realDeleteCleanRef);
                            }
                            return true;
                        } else {
                            foreach ($this->_relations[$relation] as $entry) {
                                $entry->delete();
                            }
                            return true;
                        }
                    }
                    unset($this->_relations[$relation]);
                } else {
                    if (isset($this->_relations[$relation]['id_'.$identifier])) {
                        if ($realDelete) {
                            $this->_relations[$relation]['id_'.$identifier]->delete();
                        }
                        unset($this->_relations[$relation]['id_'.$identifier]);
                        return true;
                    } elseif($realDeleteLoad === true) {
                        $tableName = $data[0].'Table';
                        $table = call_user_func($tableName . '::getInstance');
                        $table->delete($identifier);
                        return true;
                    }
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

            if (isset($data[3]) && $data[3] !== true) {
                array_unshift($values, $this->getIdentifier());
                $tableName = $data[0].'Table';
                $table = call_user_func($tableName . '::getInstance');
                $query = new MiniMVC_Query();
                $query->select('b')->from($this->_table->getModelName(), 'a')->join('a', $relation, 'b')->where('a.'.$this->_table->getIdentifier().' = ? AND b.'.$table->getIdentifier(). ' IS NOT NULL');
                if ($condition) {
                    $query->where($condition);
                }
                $query->orderBy($order)->limit($limit, $offset);
                $entries = $query->build($values);

            } else {
                array_unshift($values, $this->{$data[1]});
                $where = $data[2].' = ?' . ($condition ? ' AND '.$condition : '');
                $tableName = $data[0].'Table';
                $table = call_user_func($tableName . '::getInstance');

                $entries = $table->load($where, $values, $order, $limit, $offset);
            }
            

            foreach ($entries as $entry) {
                $this->_relations[$relation]['id_'.$entry->getIdentifier()] = $entry;
            }
            return (isset($data[3]) && $data[3] === true) ? reset($entries) : $entries;
        }
        elseif(substr($name, 0, 4) == 'save') {
            $relation = strtolower(substr($name, 4));

            $info = $this->_table->getRelation($relation);

            if (!$info || !isset($this->_relations[$relation])) {
                return false;
            }
            $tableName = $info[0].'Table';
            $table = call_user_func($tableName . '::getInstance');
            if (!isset($info[3]) || $info[3] === true) {
                if ($info[1] == $this->_table->getIdentifier()) {
                    foreach ($this->_relations[$relation] as $relation) {
                        $relation->{$info[2]} = $this->getIdentifier();
                        $relation->save();
                    }
                } elseif ($info[2] == $table->getIdentifier()) {
                    foreach ($this->_relations[$relation] as $relation) {
                        $relation->save();
                        $this->{$info[1]} = $relation->getIdentifier();                       
                    }
                }
            } else {
                if (!$this->getIdentifier()) {
                    return false;
                }
                $stmt = MiniMVC_Query::create()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ?')->execute($this->getIdentifier());
                $rows = array();
                foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
                    $rows[$row[2]] = $row[1];
                }
                foreach ($this->_relations[$relation] as $relation) {
                    $relation->save();
                    if ($relation->getIdentifier() && !isset($rows[$relation->getIdentifier()])) {
                        MiniMVC_Query::create()->insert($info[3])->set($info[1].' = ?, '.$info[2].' = ?')->execute(array($this->getIdentifier(), $relation->getIdentifier()));
                    }
                }
            }
        }
        return null;
    }
    
	public function save($relations = false)
	{
		$status = $this->_table->save($this);
 
        if ($relations && $status) {
            foreach ($this->_relations as $relation => $info) {
                $this->__call('save'.$relation, array());
            }
        }

        return $status;
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