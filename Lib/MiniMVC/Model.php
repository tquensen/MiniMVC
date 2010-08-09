<?php
class MiniMVC_Model implements ArrayAccess
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

    public function offsetSet($offset, $data)
    {
        if ($offset === null) {
            return;
        }
        if ($this->_table->getRelation($offset)) {
            $this->setRelated($offset, $data);
        } else {
            $this->_properties[$offset] = $data;
        }
    }

    public function offsetGet($offset)
    {
        if ($this->_table->getRelation($offset)) {
            return $this->getRelated($offset);
        }
        return isset($this->_properties[$offset]) ? $this->_properties[$offset] : null;
    }

    public function offsetExists($offset)
    {
        if ($this->_table->getRelation($offset)) {
            return isset($this->_relations[$offset]);
        }
        return isset($this->_properties[$offset]);
    }

    public function offsetUnset($offset)
    {
        if ($this->_table->getRelation($offset)) {
            $this->unlinkRelated($offset);
        } elseif (isset($this->_properties[$offset])) {
            unset($this->_properties[$offset]);
        }
    }

    public function __call($name, $arguments)
    {
        if (!preg_match('/^(get|set|delete|load|save|unlink|link)([\w]+)$/', $name, $matches)) {
            throw new Exception('unknown Method '.$name.' in class '.get_class($this));
        }
        $fnc = $matches[1].'Related';
        $relation = $matches[2];
        array_unshift($arguments, $relation);
        return call_user_func_array(array($this, $fnc), $arguments);
        /*
        if (substr($name, 0, 3) == 'get') {
            $relation = strtolower(substr($name, 3));
            $identifier = isset($arguments[0]) ? $arguments[0] : true;
            return $this->getRelated($relation, $identifier);
        } elseif (substr($name, 0, 3) == 'set') {
            $relation = strtolower(substr($name, 3));
            $identifier = isset($arguments[0]) ? $arguments[0] : null;
            $update = isset($arguments[1]) ? $arguments[1] : true;
            return $this->setRelated($relation, $identifier, $update);
        } elseif (substr($name, 0, 6) == 'delete') {
            $relation = strtolower(substr($name, 6));
            $identifier = isset($arguments[0]) ? $arguments[0] : true;
            $realDelete = isset($arguments[1]) ? $arguments[1] : true;
            $realDeleteLoad = isset($arguments[2]) ? $arguments[2] : false;
            $realDeleteCleanRef = isset($arguments[3]) ? $arguments[3] : false;
            return $this->deleteRelated($relation, $identifier, $realDelete, $realDeleteLoad, $realDeleteCleanRef);
        } elseif (substr($name, 0, 4) == 'load') {
            $relation = strtolower(substr($name, 4));
            $condition = isset($arguments[0]) ? $arguments[0] : null;
            $values = isset($arguments[0]) ? $arguments[0] : array();
            $order = isset($arguments[1]) ? $arguments[1] : null;
            $limit = isset($arguments[2]) ? $arguments[2] : null;
            $offset = isset($arguments[3]) ? $arguments[3] : null;
            return $this->loadRelated($relation, $condition, $values, $order, $limit, $offset);
        } elseif(substr($name, 0, 4) == 'save') {
            $relation = strtolower(substr($name, 4));
            $identifier = isset($arguments[0]) ? $arguments[0] : true;
            return $this->saveRelated($relation, $identifier);
        } elseif(substr($name, 0, 4) == 'link') {
            $relation = strtolower(substr($name, 4));
            $identifier = isset($arguments[0]) ? $arguments[0] : null;
            return $this->linkRelated($relation, $identifier);
        } elseif(substr($name, 0, 6) == 'unlink') {
            $relation = strtolower(substr($name, 6));
            $identifier = isset($arguments[0]) ? $arguments[0] : true;
            return $this->unlinkRelated($relation, $identifier);
        }
        return null;
         */
    }

    public function getRelated($relation, $identifier = true)
    {
        $relationInfo = $this->_table->getRelation($relation);
        if ($identifier === true) {
            if (isset($this->_relations[$relation])) {
                return (isset($relationInfo[3]) && $relationInfo[3] === true) ? reset($this->_relations[$relation]) : $this->_relations[$relation];
            }
        } else {
            if (isset($this->_relations[$relation]['_'.$identifier])) {
                return $this->_relations[$relation]['_'.$identifier];
            }
        }
        return ($identifier === true && (!isset($relationInfo[3]) || $relationInfo[3] !== true)) ? array() : null;
    }

    public function setRelated($relation, $identifier = null, $update = true)
    {
        if (is_object($identifier) && $identifier instanceof MiniMVC_Model) {
            if (!$identifier->getIdentifier()) {
                $this->_relations[$relation][] = $arguments[0];
                return $this;
            }
            if ($update || !isset($this->_relations[$relation]['_'.$identifier->getIdentifier()])) {
                $this->_relations[$relation]['_'.$identifier->getIdentifier()] = $identifier;
                return $this;
            }
            $info = $this->_table->getRelation($relation);

            if (!$info || !isset($this->_relations[$relation])) {
                throw new Exception('Unknown relation "'.$relation.'" for model '.$this->_table->getModelName());
            }
            if (!isset($info[3]) || $info[3] === true) {
                if ($info[1] == $this->_table->getIdentifier()) {
                    $identifier->{$info[2]} = $this->getIdentifier();
                } elseif ($info[2] == $identifier->getTable()->getIdentifier() && $identifier->getIdentifier()) {
                    $this->{$info[1]} = $identifier->getIdentifier();
                }
            }
            return $this;
        }
        throw new InvalidArgumentException('$identifier must be a MiniMVC_Model instance!');
    }

    public function deleteRelated($relation, $identifier = true, $realDelete = true, $realDeleteLoad = false, $realDeleteCleanRef = false)
    {
        if (is_object($identifier)) {
            if (isset($this->_relations[$relation]['_'.$identifier->getIdentifier()])) {
                unset($this->_relations[$relation]['_'.$identifier->getIdentifier()]);
                if (!$data = $this->getTable()->getRelation($relation)) {
                    throw new Exception('Unknown relation "'.$relation.'" for model '.$this->_table->getModelName());
                }
                if ($realDelete) {
                    $identifier->delete();
                }
                if ($data[1] != $this->getTable->getIdentifier && (!isset($data[3]) || $data[3] === true)) {
                    $this->{$data[1]} = null;
                }
                return true;
            }
        } else {
            if (!$data = $this->getTable()->getRelation($relation)) {
                throw new Exception('Unknown relation "'.$relation.'" for model '.$this->_table->getModelName());
            }
            if ($identifier === true) {
                if ($realDelete === true) {
                    if ($realDeleteLoad === true) {
                        $tableName = $data[0].'Table';
                        $table = call_user_func($tableName . '::getInstance');
                        if (isset($data[3]) && $data[3] !== true) {
                            MiniMVC_Query::create()->delete('b')->from($this->_table->getModelName(), 'a')->join('a.'.$relation, 'b')->where('a.'.$this->_table->getIdentifier().' = ? AND b.'.$table->getIdentifier(). ' IS NOT NULL')->execute($this->getIdentifier());
                            if ($realDeleteCleanRef) {
                                $table->cleanRefTables();
                            }
                        } else {
                            $table->deleteBy($data[2] . ' = ?', $this->{$data[1]}, $realDeleteCleanRef);
                        }
                    } else {
                        foreach ($this->_relations[$relation] as $entry) {
                            $entry->delete();
                        }
                    }
                }
                unset($this->_relations[$relation]);
            } else {
                if (isset($this->_relations[$relation]['_'.$identifier])) {
                    if ($realDelete) {
                        $this->_relations[$relation]['_'.$identifier]->delete();
                    }
                    unset($this->_relations[$relation]['_'.$identifier]);
                } elseif($realDeleteLoad === true) {
                    $tableName = $data[0].'Table';
                    $table = call_user_func($tableName . '::getInstance');
                    $table->delete($identifier);
                }
            }
            if ($data[1] != $this->getTable()->getIdentifier() && (!isset($data[3]) || $data[3] === true)) {
                $this->{$data[1]} = null;
            }
            return true;
        }
    }

    public function loadRelated($relation, $condition = null, $values = array(), $order = null, $limit = null, $offset = null)
    {
        if (!$data = $this->getTable()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->_table->getModelName());
        }
        if (is_string($values)) {
            $values = array($values);
        }

        if (isset($data[3]) && $data[3] !== true) {
            array_unshift($values, $this->getIdentifier());
            $tableName = $data[0].'Table';
            $table = call_user_func($tableName . '::getInstance');
            $query = new MiniMVC_Query();
            $query->select('b')->from($this->_table, 'a')->join('a.'.$relation, 'b')->where('a.'.$this->_table->getIdentifier().' = ? AND b.'.$table->getIdentifier(). ' IS NOT NULL');
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
            $this->_relations[$relation]['_'.$entry->getIdentifier()] = $entry;
        }
        return (isset($data[3]) && $data[3] === true) ? reset($entries) : $entries;
    }

    public function saveRelated($relation, $identifier = true, $saveThis = true)
    {
        if (!$info = $this->_table->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->_table->getModelName());
        }
        $this->save();
        $tableName = $info[0].'Table';
        $table = call_user_func($tableName . '::getInstance');
        if (!isset($info[3]) || $info[3] === true) {
            if ($info[1] == $this->_table->getIdentifier()) {
                if ($identifier === true) {
                    if (!isset($this->_relations[$relation])) {
                        return false;
                    }
                    foreach ($this->_relations[$relation] as $relKey => $relation) {
                        $relation->{$info[2]} = $this->getIdentifier();
                        $relation->save();
                        if (is_numeric($relKey)) {
                            $this->_relations[$relation]['_'.$relation->getIdentifier()] = $relation;
                            unset($this->_relations[$relation][$relKey]);
                        }
                    }
                } elseif (is_object($identifier)) {
                    $identifier->{$info[2]} = $this->getIdentifier();
                    $identifier->save();
                    $this->_relations[$relation]['_'.$identifier->getIdentifier()] = $identifier;
                } elseif (isset($this->_relations[$relation]['_'.$identifier])) {
                    $this->_relations[$relation]['_'.$identifier]->{$info[2]} = $this->getIdentifier();
                    $this->_relations[$relation]['_'.$identifier]->save();
                }
            } elseif ($info[2] == $table->getIdentifier()) {
                if ($identifier === true) {
                    if (!isset($this->_relations[$relation])) {
                        return false;
                    }
                    foreach ($this->_relations[$relation] as $relKey => $relation) {
                        $relation->save();
                        $this->{$info[1]} = $relation->getIdentifier();
                        if (is_numeric($relKey)) {
                            $this->_relations[$relation]['_'.$relation->getIdentifier()] = $relation;
                            unset($this->_relations[$relation][$relKey]);
                        }
                    }
                } elseif (is_object($identifier)) {
                    $identifier->save();
                    $this->{$info[1]} = $identifier->getIdentifier();
                    $this->_relations[$relation]['_'.$identifier->getIdentifier()] = $identifier;
                } elseif (isset($this->_relations[$relation]['_'.$identifier])) {
                    $this->_relations[$relation]['_'.$identifier]->save();
                    $this->{$info[1]} = $this->_relations[$relation]['_'.$identifier]->getIdentifier();
                }
            }
        } else {
            if ($identifier === true) {
                if (!isset($this->_relations[$relation])) {
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
            } elseif (is_object($identifier)) {
                $identifier->save();
                $stmt = MiniMVC_Query::create()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[1].' = ?')->execute(array($this->getIdentifier(), $identifier->getIdentifier()));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Query::create()->insert($info[3])->set($info[1].' = ?, '.$info[2].' = ?')->execute(array($this->getIdentifier(), $identifier->getIdentifier()));
                }
            } elseif (isset($this->_relations[$relation]['_'.$identifier])) {
                $this->_relations[$relation]['_'.$identifier]->save();
                $stmt = MiniMVC_Query::create()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[1].' = ?')->execute(array($this->getIdentifier(), $this->_relations[$relation]['_'.$identifier]->getIdentifier()));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Query::create()->insert($info[3])->set($info[1].' = ?, '.$info[2].' = ?')->execute(array($this->getIdentifier(), $this->_relations[$relation]['_'.$identifier]->getIdentifier()));
                }
            }
        }
    }

    public function linkRelated($relation, $identifier = null)
    {
        if (!$identifier) {
            throw new Exception('No identifier/related '.$relation.' given for model '.$this->_table->getModelName());
        }
        if (!$info = $this->_table->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->_table->getModelName());
        }
        $tableName = $info[0].'Table';
        $table = call_user_func($tableName . '::getInstance');
        if (!isset($info[3]) || $info[3] === true) {
            if ($info[1] == $this->_table->getIdentifier()) {
                if (!$this->getIdentifier()) {
                    $this->save();
                }
                if (is_object($identifier)) {
                    $identifier->{$info[2]} = $this->getIdentifier();
                    $identifier->save();
                    $this->_relations[$relation]['_'.$identifier->getIdentifier()] = $identifier;
                } elseif (isset($this->_relations[$relation]['_'.$identifier])) {
                    $this->_relations[$relation]['_'.$identifier]->{$info[2]} = $this->getIdentifier();
                    $this->_relations[$relation]['_'.$identifier]->save();
                } else {
                    if ($object = $table->getOne($identifier)) {
                        $object->{$info[2]} = $this->getIdentifier();
                        $object->save();
                    } else {
                        MiniMVC_Query::create()->update($info[0])->set($info[2].' = ?')->where($table->getIdentifier().' = ?')->execute(array($this->getIdentifier(), $identifier));
                    }
                }
            } elseif ($info[2] == $table->getIdentifier()) {
                if (is_object($identifier)) {
                    if (!$identifier->getIdentifier()) {
                        $identifier->save();
                    }
                    $this->{$info[1]} = $identifier->getIdentifier();
                    $this->_relations[$relation]['_'.$identifier->getIdentifier()] = $identifier;
                } elseif(isset($this->_relations[$relation]['_'.$identifier])) {
                    if (! $this->_relations[$relation]['_'.$identifier]->getIdentifier()) {
                         $this->_relations[$relation]['_'.$identifier]->save();
                    }
                    $this->{$info[1]} = $this->_relations[$relation]['_'.$identifier]->getIdentifier();
                } else {
                    $this->{$info[1]} = $identifier;
                    if ($object = $table->getOne($identifier)) {
                        $this->_relations[$relation]['_'.$identifier] = $object;
                    }
                }
                $this->save();
            }
        } else {
            if (is_object($identifier)) {
                if (!$this->getIdentifier()) {
                    $this->save();
                }
                if (!$identifier->getIdentifier()) {
                    $identifier->save();
                }
                $stmt = MiniMVC_Query::create()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[1].' = ?')->execute(array($this->getIdentifier(), $identifier->getIdentifier()));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Query::create()->insert($info[3])->set($info[1].' = ?, '.$info[2].' = ?')->execute(array($this->getIdentifier(), $identifier->getIdentifier()));
                }
            } elseif (isset($this->_relations[$relation]['_'.$identifier])) {
                if (!$this->getIdentifier()) {
                    $this->save();
                }
                if (!$this->_relations[$relation]['_'.$identifier]->getIdentifier()) {
                     $this->_relations[$relation]['_'.$identifier]->save();
                }
                $stmt = MiniMVC_Query::create()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[1].' = ?')->execute(array($this->getIdentifier(), $this->_relations[$relation]['_'.$identifier]->getIdentifier()));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Query::create()->insert($info[3])->set($info[1].' = ?, '.$info[2].' = ?')->execute(array($this->getIdentifier(), $this->_relations[$relation]['_'.$identifier]->getIdentifier()));
                }
            } else {
                if (!$this->getIdentifier()) {
                    $this->save();
                }
                if ($object = $table->getOne($identifier)) {
                    $this->_relations[$relation]['_'.$identifier] = $object;
                }
                $stmt = MiniMVC_Query::create()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[1].' = ?')->execute(array($this->getIdentifier(), $identifier));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Query::create()->insert($info[3])->set($info[1].' = ?, '.$info[2].' = ?')->execute(array($this->getIdentifier(), $identifier));
                }
            }
        }
    }

    public function unlinkRelated($relation, $identifier = true)
    {
        if (!$info = $this->_table->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->_table->getModelName());
        }
        $tableName = $info[0].'Table';
        $table = call_user_func($tableName . '::getInstance');
        if (!isset($info[3]) || $info[3] === true) {
            if ($info[1] == $this->_table->getIdentifier()) {
                if (is_object($identifier)) {
                    $identifier->{$info[2]} = null;
                    $identifier->save();
                    if (isset($this->_relations[$relation]['_'.$identifier->getIdentifier()])) {
                        unset($this->_relations[$relation]['_'.$identifier->getIdentifier()]);
                    }
                } elseif (isset($this->_relations[$relation]['_'.$identifier])) {
                    $this->_relations[$relation]['_'.$identifier]->{$info[2]} = null;
                    $this->_relations[$relation]['_'.$identifier]->save();
                    if (isset($this->_relations[$relation]['_'.$identifier])) {
                        unset($this->_relations[$relation]['_'.$identifier]);
                    }
                } elseif($identifier === true) {
                    if (!$this->getIdentifier()) {
                        return false;
                    }
                    MiniMVC_Query::create()->update($info[0])->set($info[2].' = ?')->where($info[2].' = ?')->execute(array(null, $this->getIdentifier()));
                    foreach ($table->get($info[2], $this->getIdentifier()) as $object) {
                        $object->{$info[2]} = null;
                    }
                } else {
                    if ($object = $table->getOne($identifier)) {
                        $object->{$info[2]} = null;
                        $object->save();
                    } else {
                        MiniMVC_Query::create()->update($info[0])->set($info[2].' = ?')->where($table->getIdentifier().' = ?')->execute(array(null, $identifier));
                    }
                }
            } elseif ($info[2] == $table->getIdentifier()) {
                if (is_object($identifier)) {
                    $this->{$info[1]} = null;
                    if (isset($this->_relations[$relation]['_'.$identifier->getIdentifier()])) {
                        unset($this->_relations[$relation]['_'.$identifier->getIdentifier()]);
                    }
                } else {
                    $this->{$info[1]} = null;
                    if (isset($this->_relations[$relation]['_'.$identifier])) {
                        unset($this->_relations[$relation]['_'.$identifier]);
                    }
                }
                $this->save();
            }
        } else {
            $this->save();
            if ($identifier === true) {
                MiniMVC_Query::create()->delete()->from($info[3])->where($info[1].' = ?')->execute($this->getIdentifier());
                unset($this->_relations[$relation]);
            } else {
                if (is_object($identifier)) {
                    if (isset($this->_relations[$relation]['_'.$identifier->getIdentifier()])) {
                        unset($this->_relations[$relation]['_'.$identifier->getIdentifier()]);
                    }
                } elseif (isset($this->_relations[$relation]['_'.$identifier])) {
                    if (isset($this->_relations[$relation]['_'.$identifier])) {
                        unset($this->_relations[$relation]['_'.$identifier]);
                    }
                }
                MiniMVC_Query::create()->delete()->from($info[3])->where($info[1].' = ? AND '.$info[2].' = ?')->execute(array($this->getIdentifier(), is_object($identifier) ? $identifier->getIdentifier() : $identifier));
            }
        }
    }
    
	public function save($relations = false)
	{
		$status = $this->_table->save($this);
 
        if ($relations && $status) {
            foreach ($this->_relations as $relation => $info) {
                $this->saveRelated($relation, array());
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