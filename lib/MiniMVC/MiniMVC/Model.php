<?php
class MiniMVC_Model implements ArrayAccess
{
	protected $_properties = array();
    protected $_databaseProperties = array();
    protected $_relations = array();
	protected $_table = null;
    protected $_collection = null;
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
        return $this->{$this->getTable()->getIdentifier()};
    }

    /**
     *
     * @return MiniMVC_Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     *
     * @return MiniMVC_Collection
     */
    public function getCollection()
    {
        if (!$this->_collection) {         
            $this->_collection = $this->getTable()->getCollection();
        }
        return $this->_collection;
    }

    public function setCollection($collection) {
        if (is_object($collection) && $collection instanceof MiniMVC_Collection) {
            $this->_collection = $collection;
            return true;
        }
        throw new InvalidArgumentException('$collection must be a MiniMVC_Collection instance!');      
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
        if ($this->getTable()->getRelation($key)) {
            return $this->getRelated($key);
        }
        return isset($this->_properties[$key]) ? $this->_properties[$key] : null;
	}

	public function __set($key, $value)
	{
		if ($this->getTable()->getRelation($key)) {
            $this->setRelated($key, $value);
        } else {
            $this->_properties[$key] = $value;
        }
	}

    public function __isset($key)
	{
        if ($this->getTable()->getRelation($key)) {
            return isset($this->_relations[$key]);
        }
        return isset($this->_properties[$key]);
	}

    public function __unset($key)
	{
        if ($this->getTable()->getRelation($key)) {
            $this->unlinkRelated($key);
        } elseif (isset($this->_properties[$key])) {
            unset($this->_properties[$key]);
        }
	}

    public function offsetSet($offset, $data)
    {
        if ($offset === null) {
            return;
        }
        if ($this->getTable()->getRelation($offset)) {
            $this->setRelated($offset, $data);
        } else {
            $this->_properties[$offset] = $data;
        }
    }

    public function offsetGet($offset)
    {
        if ($this->getTable()->getRelation($offset)) {
            return $this->getRelated($offset);
        }
        return isset($this->_properties[$offset]) ? $this->_properties[$offset] : null;
    }

    public function offsetExists($offset)
    {
        if ($this->getTable()->getRelation($offset)) {
            return isset($this->_relations[$offset]);
        }
        return isset($this->_properties[$offset]);
    }

    public function offsetUnset($offset)
    {
        if ($this->getTable()->getRelation($offset)) {
            $this->unlinkRelated($offset);
        } elseif (isset($this->_properties[$offset])) {
            unset($this->_properties[$offset]);
        }
    }

    /**
     *
     * examples:
     * $fields could look like the following:
     * array(
     *     'id', 'title', 'description'
     * )
     * or
     * array(
     *     'id', 'title', 'description', 'relations' => array(
     *          'Comments' => true, //export all fields of the related comment model
     *          'User' => array('id', 'username', 'email'), //export the id, username and email of the related users
     *          'Tags' => array(
     *              'id', 'title', 'relations' => array( //you can also fetch relations of relations
     *                  'Posts' => array('id') //get the post-ids related to each tag
     *              )
     *          )
     *      )
     * )
     *
     * @param array|bool $fields true to export all model properties or an array of property-names to export. add a key 'relations' with a value structured like this (true or array) to include related models
     * @return array
     */
    public function toArray($fields = true)
    {
        $return = array();
        foreach ($this->getTable()->getColumns() as $column) {
            if ($fields === true || (is_array($fields) && in_array($column, $fields))) {
                $return[$column] = $this->$column;
            }
        }
        if (!empty($fields['relations'])) {
            foreach ($this->getTable()->getRelations() as $relation => $relationData) {
                if ($fields['relations'] === true || (is_array($fields['relations']) && isset($fields['relations'][$relation]))) {
                    $currentRelationEntries = $this->getRelated($relation, true, false);
                    if (is_array($currentRelationEntries) || (is_object($currentRelationEntries) && $currentRelationEntries instanceof MiniMVC_Collection)) {
                        foreach ($currentRelationEntries as $k => $v) {
                            $currentRelationEntries[$k] = $v->toArray($fields['relations'] === true ? true : $fields['relations'][$relation]);
                        }
                    } elseif(is_object($currentRelationEntries)) {
                        $currentRelationEntries = $currentRelationEntries->toArray($fields['relations'] === true ? true : $fields['relations'][$relation]);
                    }
                    $return[$relation] = $currentRelationEntries;
                }
            }
        }
        return $return;
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

    /**
     *
     * @param string $relation the name of a relation
     * @param mixed $identifier the identifier of the related model or true to return all stored models of this relation
     * @param bool $load true (default) to load the related entries from db if not already there
     * @return MiniMVC_Model|array
     */
    public function getRelated($relation, $identifier = true, $load = true)
    {
        if (!$relationInfo = $this->getTable()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->getTable()->getModelName());
        }
        if ($identifier === true) {
            if (isset($this->_relations[$relation])) {
                return (isset($relationInfo[3]) && $relationInfo[3] === true) ? $this->_relations[$relation]->getFirst() : $this->_relations[$relation];
            } elseif ($load) {
                return $this->loadRelated($relation);
            }
        } else {
            if (isset($this->_relations[$relation]) && $entry = $this->_relations[$relation][$identifier]) {
                return $entry;
            } elseif ($load && !isset($this->_relations[$relation])) {
                $tableName = $relationInfo[0].'Table';
                $table = call_user_func($tableName . '::getInstance');
                return $this->loadRelated($relation, $table->getIdentifier().' = ?', $identifier);

            }
        }

        if ($identifier !== true || (isset($relationInfo[3]) && $relationInfo[3] === true)) {
            return null;
        }
        $collectionName = $relationInfo[0].'Collection';
        $collection = call_user_func($collectionName . '::getInstance');
        return $collection;
    }

    /**
     *
     * @param string $relation the name of a relation
     * @param MiniMVC_Model|array $identifier the related model or an array of models
     * @param bool $update whether to update the model if it is already stored or not
     */
    public function setRelated($relation, $identifier = null, $update = true)
    {
        if (is_array($identifier) || $identifier instanceof MiniMVC_Collection) {
            foreach ($identifier as $id) {
                $this->setRelated($relation, $id, $update);
            }
        }
        if (is_object($identifier) && $identifier instanceof MiniMVC_Model) {
            $info = $this->getTable()->getRelation($relation);

            if (!$info || !isset($this->_relations[$relation])) {
                throw new Exception('Unknown relation "'.$relation.'" for model '.$this->getTable()->getModelName());
            }

            if (!isset($this->_relations[$relation])) {
                $this->_relations[$relation] = $identifier->getTable()->getCollection();
            }

            if (!$identifier->getIdentifier()) {
                $this->_relations[$relation][] = $arguments[0];
                return true;
            }
            if ($update || !isset($this->_relations[$relation][$identifier->getIdentifier()])) {
                $this->_relations[$relation][$identifier->getIdentifier()] = $identifier;

                if (!isset($info[3]) || $info[3] === true) {
                    if ($info[1] == $this->getTable()->getIdentifier()) {
                        $identifier->{$info[2]} = $this->getIdentifier();
                    } elseif ($info[2] == $identifier->getTable()->getIdentifier() && $identifier->getIdentifier()) {
                        $this->{$info[1]} = $identifier->getIdentifier();
                    }
                }
            }
          
            return true;
        }
        throw new InvalidArgumentException('$identifier must be a MiniMVC_Model instance!');
    }

    /**
     *
     * @param string $relation the name of a relation
     * @param mixed $identifier either a model object, an identifier of a related model or true
     * @param bool $realDelete whether to delete the model from the database (true) or just from this object(false) defaults to true
     * @param bool $realDeleteLoad if the identifier is true only the related models currently assigned to this object will be deleted. with relaDeleteLoad=true, all related models will be deleted
     * @param bool $realDeleteCleanRef if relaDeleteLoad is true, set realDeleteCleanRef=true to clean up the ref table (for m:n relations)
     */
    public function deleteRelated($relation, $identifier = true, $realDelete = true, $realDeleteLoad = false, $realDeleteCleanRef = false)
    {
        if (is_array($identifier)) {
            foreach ($identifier as $id) {
                $this->deleteRelated($relation, $id, $realDelete, $realDeleteLoad, $realDeleteCleanRef);
            }
        }
        if (is_object($identifier)) {
            if (isset($this->_relations[$relation][$identifier->getIdentifier()])) {
                unset($this->_relations[$relation][$identifier->getIdentifier()]);
                if (!$data = $this->getTable()->getRelation($relation)) {
                    throw new Exception('Unknown relation "'.$relation.'" for model '.$this->getTable()->getModelName());
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
                throw new Exception('Unknown relation "'.$relation.'" for model '.$this->getTable()->getModelName());
            }
            if ($identifier === true) {
                if ($realDelete === true) {
                    if ($realDeleteLoad === true) {
                        $tableName = $data[0].'Table';
                        $table = call_user_func($tableName . '::getInstance');
                        if (isset($data[3]) && $data[3] !== true) {
                            $stmt = $this->getTable()->query('a', false)->select('b.'.$table->getIdentifier())->join('a.'.$relation, 'b')->where('a.'.$this->getTable()->getIdentifier().' = ? AND b.'.$table->getIdentifier(). ' IS NOT NULL')->execute($this->getIdentifier());
                            $relatedTableIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            //$this->getTable()->query('a', false)->delete('b')->join('a.'.$relation, 'b')->where('a.'.$this->getTable()->getIdentifier().' = ? AND b.'.$table->getIdentifier(). ' IS NOT NULL')->execute($this->getIdentifier());
                            $deleteStmt = MiniMVC_Registry::getInstance()->db->query()->delete($table)->where($table->getIdentifier() . ' = ?')->prepare();
                            foreach ($relatedTableIds as $relatedTableId) {
                                $deleteStmt->execute(array($relatedTableId));
                            }
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
                if (isset($this->_relations[$relation][$identifier])) {
                    if ($realDelete) {
                        $this->_relations[$relation][$identifier]->delete();
                    }
                    unset($this->_relations[$relation][$identifier]);
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

    /**
     *
     * @param string $relation the name of a relation
     * @param string $condition the where-condition
     * @param array $values values for the placeholders in the condition
     * @param string $order the order
     * @param int $limit the limit
     * @param int $offset the offset
     * @return MiniMVC_Model|MiniMVC_Collection
     */
    public function loadRelated($relation, $condition = null, $values = array(), $order = null, $limit = null, $offset = null)
    {
        if (!$data = $this->getTable()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->getTable()->getModelName());
        }
        if (!is_array($values)) {
            $values = (array) $values;
        }

        if (isset($data[3]) && $data[3] !== true) {
            array_unshift($values, $this->getIdentifier());
            $tableName = $data[0].'Table';
            $table = call_user_func($tableName . '::getInstance');
            $query = $this->getTable()->query('b', 'a')->join('b.'.$relation, 'a')->where('b.'.$this->getTable()->getIdentifier().' = ? AND a.'.$table->getIdentifier(). ' IS NOT NULL');
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

        if (isset($this->_relations[$relation])) {
            $this->_relations[$relation]->add($entries);
        } else {
            $this->_relations[$relation] = $entries;
        }
        return (isset($data[3]) && $data[3] === true) ? $entries->getFirst() : $entries;
    }

    /**
     *
     * @param string $relation the name of a relation
     * @param mixed $identifier a related model object, the identifier of a related model currently asigned to this model or true to save all related models
     * @param bool $saveThisOnDemand whether to allow this model to be saved in the database if its new (to generate an auto-increment identifier)
     */
    public function saveRelated($relation, $identifier = true, $saveThisOnDemand = true)
    {
        if (is_array($identifier)) {
            foreach ($identifier as $id) {
                $this->saveRelated($relation, $id, $saveThisOnDemand);
            }
        }
        
        if (!$info = $this->getTable()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->getTable()->getModelName());
        }

        //$this->save();
        $tableName = $info[0].'Table';
        $table = call_user_func($tableName . '::getInstance');
        if (!isset($info[3]) || $info[3] === true) {
            if ($info[1] == $this->getTable()->getIdentifier()) {
                if (!$this->getIdentifier()) {
                    if (!$saveThisOnDemand) {
                        return false;
                    }
                    $this->save();
                }
                if ($identifier === true) {
                    if (!isset($this->_relations[$relation])) {
                        return false;
                    }

                    foreach ($this->_relations[$relation] as $relKey => $relation) {
                        $relation->{$info[2]} = $this->getIdentifier();
                        $relation->save();
                        if (is_numeric($relKey) && $relKey < 0) {
                            $this->_relations[$relation][$relation->getIdentifier()] = $relation;
                            unset($this->_relations[$relation][$relKey]);
                        }
                    }
                } elseif (is_object($identifier)) {
                    $identifier->{$info[2]} = $this->getIdentifier();
                    $identifier->save();
                    if (!isset($this->_relations[$relation])) {
                        $this->_relations[$relation] = $identifier->getTable()->getCollection();
                    }
                    $this->_relations[$relation][$identifier->getIdentifier()] = $identifier;
                } elseif (isset($this->_relations[$relation][$identifier])) {
                    $this->_relations[$relation][$identifier]->{$info[2]} = $this->getIdentifier();
                    $this->_relations[$relation][$identifier]->save();
                }
            } elseif ($info[2] == $table->getIdentifier()) {
                if ($identifier === true) {
                    if (!isset($this->_relations[$relation])) {
                        return false;
                    }
                    foreach ($this->_relations[$relation] as $relKey => $relation) {
                        $relation->save();
                        $this->{$info[1]} = $relation->getIdentifier();
                        if (is_numeric($relKey) && $relKey < 0) {
                            $this->_relations[$relation][$relation->getIdentifier()] = $relation;
                            unset($this->_relations[$relation][$relKey]);
                        }
                    }
                } elseif (is_object($identifier)) {
                    $identifier->save();
                    $this->{$info[1]} = $identifier->getIdentifier();
                    if (!isset($this->_relations[$relation])) {
                        $this->_relations[$relation] = $identifier->getTable()->getCollection();
                    }
                    $this->_relations[$relation][$identifier->getIdentifier()] = $identifier;
                } elseif (isset($this->_relations[$relation][$identifier])) {
                    $this->_relations[$relation][$identifier]->save();
                    $this->{$info[1]} = $this->_relations[$relation][$identifier]->getIdentifier();
                }
            }
        } else {
            if ($identifier === true) {
                if (!isset($this->_relations[$relation])) {
                    return false;
                }
                if (!$this->getIdentifier()) {
                    if (!$saveThisOnDemand) {
                        return false;
                    }
                    $this->save();
                }
                $stmt = MiniMVC_Registry::getInstance()->db->query()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ?')->execute($this->getIdentifier());
                $rows = array();
                foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
                    $rows[$row[2]] = $row[1];
                }
                foreach ($this->_relations[$relation] as $relation) {
                    $relation->save();
                    if ($relation->getIdentifier() && !isset($rows[$relation->getIdentifier()])) {
                        MiniMVC_Registry::getInstance()->db->query()->insert(array($info[1], $info[2]), array($this->getIdentifier(), $relation->getIdentifier()), $info[3])->execute();
                    }
                }
            } elseif (is_object($identifier)) {
                $identifier->save();
                $stmt = MiniMVC_Registry::getInstance()->db->query()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[2].' = ?')->execute(array($this->getIdentifier(), $identifier->getIdentifier()));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Registry::getInstance()->db->query()->insert(array($info[1], $info[2]), array($this->getIdentifier(), $identifier->getIdentifier()), $info[3])->execute();
                }
                if (!isset($this->_relations[$relation])) {
                    $this->_relations[$relation] = $identifier->getTable()->getCollection();
                }
                $this->_relations[$relation][$identifier->getIdentifier()] = $identifier;
            } elseif (isset($this->_relations[$relation][$identifier])) {
                $this->_relations[$relation][$identifier]->save();
                $stmt = MiniMVC_Registry::getInstance()->db->query()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[2].' = ?')->execute(array($this->getIdentifier(), $this->_relations[$relation]['_'.$identifier]->getIdentifier()));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Registry::getInstance()->db->query()->insert(array($info[1], $info[2]), array($this->getIdentifier(), $this->_relations[$relation]['_'.$identifier]->getIdentifier()), $info[3])->execute();
                }
            }
        }
    }

    /**
     *
     * @param string $relation the name of a relation
     * @param mixed $identifier a related model object, the identifier of a related model
     * @param bool $loadRelated whether to load the related object (if identifier is not already loaded and assigned to this model)
     */
    public function linkRelated($relation, $identifier = null, $loadRelated = false)
    {
        if (is_array($identifier)) {
            foreach ($identifier as $id) {
                $this->linkRelated($relation, $id, $loadRelated);
            }
        }
        
        if (!$identifier) {
            throw new Exception('No identifier/related '.$relation.' given for model '.$this->getTable()->getModelName());
        }
        if (!$info = $this->getTable()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->getTable()->getModelName());
        }

        $tableName = $info[0].'Table';
        $table = call_user_func($tableName . '::getInstance');
        if (!isset($info[3]) || $info[3] === true) {
            if ($info[1] == $this->getTable()->getIdentifier()) {
                if (!$this->getIdentifier()) {
                    $this->save();
                }
                if (is_object($identifier)) {
                    $identifier->{$info[2]} = $this->getIdentifier();
                    $identifier->save();
                    if (!isset($this->_relations[$relation])) {
                        $this->_relations[$relation] = $identifier->getTable()->getCollection();
                    }
                    $this->_relations[$relation][$identifier->getIdentifier()] = $identifier;
                } elseif (isset($this->_relations[$relation][$identifier])) {
                    $this->_relations[$relation][$identifier]->{$info[2]} = $this->getIdentifier();
                    $this->_relations[$relation][$identifier]->save();
                } else {
                    if ($loadRelated && $object = $table->loadOne($identifier)) {
                        $object->{$info[2]} = $this->getIdentifier();
                        $object->save();
                        if (!isset($this->_relations[$relation])) {
                            $this->_relations[$relation] = $object->getTable()->getCollection();
                        }
                        $this->_relations[$relation][$object->getIdentifier()] = $object;
                    } else {
                        $table->query()->update($info[2])->where($table->getIdentifier().' = ?')->execute(array($this->getIdentifier(), $identifier));
                        //MiniMVC_Registry::getInstance()->db->query()->update($info[0], $info[2])->where($table->getIdentifier().' = ?')->execute(array($this->getIdentifier(), $identifier));
                    }
                }
            } elseif ($info[2] == $table->getIdentifier()) {
                if (is_object($identifier)) {
                    if (!$identifier->getIdentifier()) {
                        $identifier->save();
                    }
                    $this->{$info[1]} = $identifier->getIdentifier();
                    if (!isset($this->_relations[$relation])) {
                        $this->_relations[$relation] = $identifier->getTable()->getCollection();
                    }
                    $this->_relations[$relation][$identifier->getIdentifier()] = $identifier;
                } elseif(isset($this->_relations[$relation][$identifier])) {
                    if (! $this->_relations[$relation][$identifier]->getIdentifier()) {
                         $this->_relations[$relation][$identifier]->save();
                    }
                    $this->{$info[1]} = $this->_relations[$relation][$identifier]->getIdentifier();
                } else {
                    $this->{$info[1]} = $identifier;
                    if ($loadRelated && $object = $table->loadOne($identifier)) {
                        if (!isset($this->_relations[$relation])) {
                            $this->_relations[$relation] = $object->getTable()->getCollection();
                        }
                        $this->_relations[$relation][$identifier] = $object;
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
                $stmt = MiniMVC_Registry::getInstance()->db->query()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[2].' = ?')->execute(array($this->getIdentifier(), $identifier->getIdentifier()));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Registry::getInstance()->db->query()->insert(array($info[1], $info[2]), array($this->getIdentifier(), $identifier->getIdentifier()), $info[3])->execute();
                }
                if (!isset($this->_relations[$relation])) {
                    $this->_relations[$relation] = $identifier->getTable()->getCollection();
                }
                $this->_relations[$relation][$identifier->getIdentifier()] = $identifier;
            } elseif (isset($this->_relations[$relation][$identifier])) {
                if (!$this->getIdentifier()) {
                    $this->save();
                }
                if (!$this->_relations[$relation][$identifier]->getIdentifier()) {
                     $this->_relations[$relation][$identifier]->save();
                }
                $stmt = MiniMVC_Registry::getInstance()->db->query()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[1].' = ?')->execute(array($this->getIdentifier(), $this->_relations[$relation]['_'.$identifier]->getIdentifier()));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();
                if (!$result) {
                    MiniMVC_Registry::getInstance()->db->query()->insert(array($info[1], $info[2]), array($this->getIdentifier(), $this->_relations[$relation]['_'.$identifier]->getIdentifier()), $info[3])->execute();
                }
            } else {
                if (!$this->getIdentifier()) {
                    $this->save();
                }
                if ($loadRelated && $object = $table->loadOne($identifier)) {
                    if (!isset($this->_relations[$relation])) {
                        $this->_relations[$relation] = $object->getTable()->getCollection();
                    }
                    $this->_relations[$relation][$identifier] = $object;
                }
                $stmt = MiniMVC_Registry::getInstance()->db->query()->select('id, '.$info[1].', '.$info[2])->from($info[3])->where($info[1].' = ? AND '.$info[2].' = ?')->execute(array($this->getIdentifier(), $identifier));
                $result = $stmt->fetch(PDO::FETCH_NUM);
                $stmt->closeCursor();

                if (!$result) {
                    MiniMVC_Registry::getInstance()->db->query()->insert(array($info[1], $info[2]), array($this->getIdentifier(), $identifier), $info[3])->execute();
                }
            }
        }
    }

    /**
     *
     * @param string $relation the name of a relation
     * @param mixed $identifier a related model object, the identifier of a related model or true to unlink all related models
     */
    public function unlinkRelated($relation, $identifier = true)
    {
        if (is_array($identifier)) {
            foreach ($identifier as $id) {
                $this->unlinkRelated($relation, $id);
            }
        }

        if (!$info = $this->getTable()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.$this->getTable()->getModelName());
        }
        $tableName = $info[0].'Table';
        $table = call_user_func($tableName . '::getInstance');
        if (!isset($info[3]) || $info[3] === true) {
            if ($info[1] == $this->getTable()->getIdentifier()) {
                if (is_object($identifier)) {
                    $identifier->{$info[2]} = null;
                    $identifier->save();
                    if (isset($this->_relations[$relation][$identifier->getIdentifier()])) {
                        unset($this->_relations[$relation][$identifier->getIdentifier()]);
                    }
                } elseif (isset($this->_relations[$relation][$identifier])) {
                    $this->_relations[$relation][$identifier]->{$info[2]} = null;
                    $this->_relations[$relation][$identifier]->save();
                    unset($this->_relations[$relation][$identifier]);
                    
                } elseif($identifier === true) {
                    if (!$this->getIdentifier()) {
                        return false;
                    }
                    $table->query()->update($info[2])->where($info[2].' = ?')->execute(array(null, $this->getIdentifier()));
                    //MiniMVC_Registry::getInstance()->db->query()->update($info[0], $info[2])->where($info[2].' = ?')->execute(array(null, $this->getIdentifier()));
                } else {
                    $table->query()->update($info[2])->where($table->getIdentifier().' = ?')->execute(array(null, $identifier));
                    //MiniMVC_Registry::getInstance()->db->query()->update($info[0], $info[2])->where($table->getIdentifier().' = ?')->execute(array(null, $identifier));
                }
            } elseif ($info[2] == $table->getIdentifier()) {
                if (is_object($identifier)) {
                    $this->{$info[1]} = null;
                    if (isset($this->_relations[$relation][$identifier->getIdentifier()])) {
                        unset($this->_relations[$relation][$identifier->getIdentifier()]);
                    }
                } else {
                    $this->{$info[1]} = null;
                    if (isset($this->_relations[$relation][$identifier])) {
                        unset($this->_relations[$relation][$identifier]);
                    }
                }
                $this->save();
            }
        } else {
            if (!$this->getIdentifier()) {
                return false;
            }
            if ($identifier === true) {
                MiniMVC_Registry::getInstance()->db->query()->delete($info[3])->where($info[1].' = ?')->execute($this->getIdentifier());
                unset($this->_relations[$relation]);
            } else {
                if (is_object($identifier)) {
                    if (isset($this->_relations[$relation][$identifier->getIdentifier()])) {
                        unset($this->_relations[$relation][$identifier->getIdentifier()]);
                    }
                } elseif (isset($this->_relations[$relation][$identifier])) {
                    unset($this->_relations[$relation][$identifier]);
                }
                MiniMVC_Registry::getInstance()->db->query()->delete($info[3])->where($info[1].' = ? AND '.$info[2].' = ?')->execute(array($this->getIdentifier(), is_object($identifier) ? $identifier->getIdentifier() : $identifier));
            }
        }
    }

	public function save($relations = false)
	{
		$status = $this->getTable()->save($this);

        if ($relations && $status) {
            foreach ($this->_relations as $relation => $info) {
                $this->saveRelated($relation, true);
            }
        }

        return $status;
	}

	public function delete()
	{
		return $this->getTable()->delete($this);
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