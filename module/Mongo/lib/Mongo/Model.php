<?php
/**
 * Mongo_Model is the base class for mongodb models
 *
 * @property MongoId $_id
 *
 */
class Mongo_Model implements Serializable
{

    protected $_properties = array();
    protected $_databaseProperties = array();
    protected $_connection = null;
    /**
     *
     * @var Mongo_Repository
     */
    protected $_repository = null;

    public function __construct($data = array(), $connection = null, $repository = false)
    {
        $this->_properties = $data;
        $this->_connection = $connection;
        if ($repository) {
            $this->_repository = $repository;
        }
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
        unset($this->_properties[$key]);
	}

    public function &getData()
    {
        return $this->_properties;
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

    public function toArray()
    {
        return $this->getData();
    }
    
    public function serialize()
    {
        return serialize(array(
            'p' => $this->_properties,
            'dbp' => $this->_databaseProperties,
            'con' => $this->_repository->getConnection()
        ));
    }
    
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->__construct($data['p'], $data['con']);
        $this->_databaseProperties = $data['dbp'];
    }
    /**
     *
     * @return Mongo_Repository
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            $repositoryName = get_class($this).'Repository';
            $this->_repository = class_exists($repositoryName) ? new $repositoryName(null, null, $this->_connection) : new Mongo_Repository(get_class($this), get_class($this), $this->_connection);    
        }
        return $this->_repository;
    }

    /**
     *
     * @return MongoDB
     */
    public function getDB()
    {
        return $this->getRepository()->getDB();
    }

    /**
     *
     * @return MongoCollection
     */
    public function getCollection()
    {
        return $this->getRepository()->getCollection();
    }

    public function increment($property, $value, $save = true)
    {
        $this->$property = $this->$property + $value;
        if ($save !== null) {
            $status = $this->getCollection()->update(array('_id' => $this->_id), array('$inc' => array($property => $value)), array('safe' => $save));
            if ($status) {
                $this->setDatabaseProperty($property, $this->$property);
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $relation the relation name
     * @param array $query Additional fields to filter.
     * @param array $sort The fields by which to sort.
     * @param int $limit The number of results to return.
     * @param int $skip The number of results to skip.
     * @return Mongo_Model|array
     */
    public function getRelated($relation, $query = array(), $sort = array(), $limit = null, $skip = null)
    {
        if (!$relationInfo = $this->getRepository()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.get_class($this));
        }
        $repositoryName = $relationInfo[0].'Repository';
        $repository = class_exists($repositoryName)
            ? new $repositoryName(null, null, $this->getRepository()->getConnection())
            : new Mongo_Repository($relationInfo[0], $relationInfo[0], $this->getRepository()->getConnection());
            
        if (!empty($relationInfo[3])) {
            return $repository->findOne(array($relationInfo[2] => $this->{$relationInfo[1]}));
        } else {
            $query = (array) $query;
            if ($relationInfo[2] == '_id' && is_array($this->{$relationInfo[1]})) {
                $query[$relationInfo[2]] = array('$in' => $this->{$relationInfo[1]});
            } else {
                $query[$relationInfo[2]] = $this->{$relationInfo[1]};
            }            
            return $repository->find($query, $sort, $limit, $skip);
        }
    }

    /**
     *
     * @param string $relation the relation name
     * @param Mongo_Model|mixed $related either a Mongo_Model object, a Mongo_Model->_id-value or an array with multiple Mongo_Models
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @param bool $multiple true to store multiple related as array (m:n), false to only store a single value (1:1, n:1, default)
     * @return bool
     */
    public function setRelated($relation, $related, $save = true, $multiple = false)
    {
        if (!$relationInfo = $this->getRepository()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.get_class($this));
        }
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->setRelated($relation, $rel, $save, $multiple);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof Mongo_Model)) {
            $repositoryName = $relationInfo[0].'Repository';
            $repository = class_exists($repositoryName)
                ? new $repositoryName(null, null, $this->getRepository()->getConnection())
                : new Mongo_Repository($relationInfo[0], $relationInfo[0], $this->getRepository()->getConnection());
            $related = $repository->findOne($related);
            if (!$related) {
                throw new InvalidArgumentException('Could not find valid '.$relationInfo[0]);
            }
        }
        if (!empty($relationInfo[3])) {
            if ($relationInfo[1] == '_id') {
                if (!$this->{$relationInfo[1]}) {
                    if (!$this->getRepository()->isAutoId()) {
                        throw new Exception('Counld not set realted '.$relationInfo[0].' - '.$relationInfo[1].' not set!');
                    }
                    $this->{$relationInfo[1]} = new MongoId();
                    if ($save !== null) {
                        $this->save($save);
                    }
                }
                
                $related->{$relationInfo[2]} = $this->{$relationInfo[1]};
                return $save !== null ? $related->save($save) : true;
            } elseif ($relationInfo[2] == '_id') {
                if (!$related->{$relationInfo[2]}) {
                    if (!$related->getRepository()->isAutoId()) {
                        throw new Exception('Counld not set realted '.$relationInfo[0].' - '.$relationInfo[2].' not set!');
                    }
                    $related->{$relationInfo[2]} = new MongoId();
                    if ($save !== null) {
                        $related->save($save);
                    }
                }
                $this->{$relationInfo[1]} = $related->{$relationInfo[2]};
                return $save !== null ? $this->save($save) : true;
            }
        } else {
            if ($relationInfo[1] == '_id' && !$this->{$relationInfo[1]}) {
                if (!$this->getRepository()->isAutoId()) {
                    throw new Exception('Counld not set realted '.$relationInfo[0].' - '.$relationInfo[1].' not set!');
                }
                $this->{$relationInfo[1]} = new MongoId();
                if ($save !== null) {
                    $this->save($save);
                }
            } elseif ($relationInfo[2] == '_id' && !$related->{$relationInfo[2]}) {
                if (!$related->getRepository()->isAutoId()) {
                    throw new Exception('Counld not set realted '.$relationInfo[0].' - '.$relationInfo[2].' not set!');
                }
                $related->{$relationInfo[2]} = new MongoId();
                if ($save !== null) {
                    $related->save($save);
                }
            }
            if ($relationInfo[1] == '_id') {
                if ($multiple) {
                    $rels = (array) $related->{$relationInfo[2]};
                    if (!in_array($this->{$relationInfo[1]}, $rels)) {
                        $rels[] = $this->{$relationInfo[1]};
                        $rels = array_values($rels);
                        $related->{$relationInfo[2]} = $rels;
                    }
                } else {
                    $related->{$relationInfo[2]} = $this->{$relationInfo[1]};                    
                }
                return $save !== null ? $related->save($save) : true;
            } else {
                if ($multiple) {
                    $rels = (array) $this->{$relationInfo[1]};
                    if (!in_array($related->{$relationInfo[2]}, $rels)) {
                        $rels[] = $related->{$relationInfo[2]};
                        $rels = array_values($rels);
                        $this->{$relationInfo[1]} = $rels;
                    }
                } else {
                    $this->{$relationInfo[1]} == $related->{$relationInfo[2]};
                }
                return $save !== null ? $this->save($save) : true;
            }
        }
    }

    /**
     *
     * @param string $relation the relation name
     * @param Mongo_Model|mixed $related true to remove all objects or either a Mongo_Model object, a Mongo_Model->_id-value  or an array with multiple Mongo_Models
     * @param boolean $delete true to delete the related entry, false to only remove the relation (default false) 
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function removeRelated($relation, $related = true, $delete = false, $save = true)
    {
        if (!$relationInfo = $this->getRepository()->getRelation($relation)) {
            throw new Exception('Unknown relation "'.$relation.'" for model '.get_class($this));
        }
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->removeRelated($relation, $rel, $delete, $save);
            }
            return true;
        }
        if (!empty($relationInfo[3])) {
            
            $repositoryName = $relationInfo[0].'Repository';
            $repository = class_exists($repositoryName)
                ? new $repositoryName(null, null, $this->getRepository()->getConnection())
                : new Mongo_Repository($relationInfo[0], $relationInfo[0], $this->getRepository()->getConnection());

                
            if ($relationInfo[1] == '_id') {
                if (!$this->{$relationInfo[1]} || $save === null) {
                    return true;
                }
                
                $query = array($relationInfo[2] => $this->{$relationInfo[1]});
                $options = $save !== null ? array('safe' => $save) : array();
                if ($related !== true) {
                    if (!is_object($related) || !($related instanceof Mongo_Model)) {
                        $query['_id'] = $related;
                    } else {
                        $query['_id'] = $related->_id;
                    }
                }
                if ($delete) {
                    return $repository->getCollection()->remove($query, $options);
                } else {
                    return $repository->getCollection()->update($query, array('$set' => array($relationInfo[2] => null)), $options);
                }
            } else {
                if ($related !== true) {
                    if (is_object($related) && $related instanceof Mongo_Model) {
                        $related = $related->_id;
                    }
                    if ($this->{$relationInfo[1]} != $related) {
                        return false;
                    }
                }
                
                if ($delete) {
                    $query = array($relationInfo[2] => $this->{$relationInfo[1]});
                    $options = $save !== null ? array('safe' => $save) : array();
                    if (!$repository->getCollection()->remove($query, $options)) {
                        return false;
                    }    
                }
                $this->{$relationInfo[1]} = null;
                return $save !== null ? $this->save($save) : true;
            }
        } else {
            if ($related === true) {
                if ($relationInfo[2] == '_id') {                    
                    if ($delete) {
                        $repositoryName = $relationInfo[0].'Repository';
                        $repository = class_exists($repositoryName)
                            ? new $repositoryName(null, null, $this->getRepository()->getConnection())
                            : new Mongo_Repository($relationInfo[0], $relationInfo[0], $this->getRepository()->getConnection());
                        
                        $options = $save !== null ? array('safe' => $save) : array();
                        if (is_array($this->{$relationInfo[1]})) {
                            $status = (bool) $repository->getCollection()->remove(array($relationInfo[2] => array('$in' => $this->{$relationInfo[1]})), $options);
                        } else {
                            $status = (bool) $repository->getCollection()->remove(array($relationInfo[2] => $this->{$relationInfo[1]}), $options);
                        }
                        
                        if (!$status) {
                            return false;
                        }                       
                    }
                    $this->{$relationInfo[1]} = null;
                    return $save !== null ? $this->save($save) : true;
                } else {
                    $repositoryName = $relationInfo[0].'Repository';
                    $repository = class_exists($repositoryName)
                        ? new $repositoryName(null, null, $this->getRepository()->getConnection())
                        : new Mongo_Repository($relationInfo[0], $relationInfo[0], $this->getRepository()->getConnection());
                    $related = $repository->find(array($relationInfo[2] => $this->{$relationInfo[1]}));
                    foreach ($related as $rel) {
                        if (is_array($related->{$relationInfo[2]})) {
                            $rels = $related->{$relationInfo[2]};
                            if ($k = array_search($this->{$relationInfo[1]}, $rels)) {
                                unset($rels[$k]);
                                $rels = array_values($rels);
                            }
                            $rel->{$relationInfo[2]} = $rels;
                        } else {
                            $rel->{$relationInfo[2]} = null;
                        }
                        if ($delete && !$rel->{$relationInfo[2]} && $save !== null) {
                            $rel->remove($save);
                        } elseif ($save !== null) {
                            $rel->save($save);
                        }
                    }
                }
                return true;
            } else {
                if (!is_object($related) || !($related instanceof Mongo_Model)) {
                    $repositoryName = $relationInfo[0].'Repository';
                    $repository = class_exists($repositoryName)
                        ? new $repositoryName(null, null, $this->getRepository()->getConnection())
                        : new Mongo_Repository($relationInfo[0], $relationInfo[0], $this->getRepository()->getConnection());
                    $related = $repository->findOne($related);
                }
                if (!$related) {
                    return false;
                }
                if ($related->{$relationInfo[2]} != $this->{$relationInfo[1]} && !is_array($related->{$relationInfo[2]}) && !is_array($this->{$relationInfo[1]})) {
                    return false;
                }
                if ($relationInfo[1] == '_id') {
                    if (is_array($related->{$relationInfo[2]})) {
                        $rels = $related->{$relationInfo[2]};
                        if ($k = array_search($this->{$relationInfo[1]}, $rels)) {
                            unset($rels[$k]);
                            $rels = array_values($rels);
                        }
                        $related->{$relationInfo[2]} = $rels;
                    } elseif($related->{$relationInfo[2]} == $this->{$relationInfo[1]}) {
                        $related->{$relationInfo[2]} = null;
                    } else {
                        return false;
                    }
                    if ($delete && !$related->{$relationInfo[2]} && $save !== null) {
                        $related->remove($save);
                    } elseif ($save !== null) {
                        return $related->save($save);
                    } else {
                        return true;
                    }
                } else {
                    if (is_array($this->{$relationInfo[1]})) {
                        $rels = $this->{$relationInfo[1]};
                        if ($k = array_search($related->{$relationInfo[2]}, $rels)) {
                            unset($rels[$k]);
                            $rels = array_values($rels);
                        }
                        $this->{$relationInfo[1]} = $rels;
                    } elseif($related->{$relationInfo[2]} == $this->{$relationInfo[1]}) {
                        $this->{$relationInfo[1]} = null;
                    } else {
                        return false;
                    }
                    if ($delete && $save !== null) {
                        $related->remove($save);
                    } 
                    return $save !== null ? $this->save($save) : true;
                }
            }
        }
    }

    /**
     *
     * @param string $embedded the embedded name
     * @param int|bool $key the identifier of a embedded or true to return all
     * @param string $sortBy (optional) if $key == true, order the entries by this property, null to keep the db order
     * @param bool $sortDesc false (default) to sort ascending, true to sort descending
     * @return Mongo_Embedded|array
     */
    public function getEmbedded($embedded, $key = true, $sortBy = null, $sortDesc = false)
    {
        if (!$embeddedInfo = $this->getRepository()->getEmbedded($embedded)) {
            throw new Exception('Unknown embedded "'.$embedded.'" for model '.get_class($this));
        }
        $className = $embeddedInfo[0];
        if (!empty($embeddedInfo[3])) {            
            return !empty($this->{$embeddedInfo[1]}) ? new $className($this->{$embeddedInfo[1]}) : null;
        } else {
            if ($key !== true) {
                foreach ((array) $this->{$embeddedInfo[1]} as $data) {
                    if (isset($data[$embeddedInfo[2]]) && $data[$embeddedInfo[2]] == $key) {
                        return new $className($data);
                    }
                }
                return null;
            } else {
                $return = array();
                foreach ((array) $this->{$embeddedInfo[1]} as $data) {
                    if (isset($data[$embeddedInfo[2]])) {
                        $return[$data[$embeddedInfo[2]]] = new $className($data);
                    } else {
                        $return[] = new $className($data);
                    }
                }
                if (is_string($sortBy)) {
                    $return = $className->sort($return, $sortBy, (bool) $sortDesc);
                }
                return $return;
            }
        }
    }

    /**
     *
     * @param string $embedded the embedded name
     * @param Mongo_Embedded|array $data an array of Mongo_Embedded objects or an array representing a Mongo_Embedded or an array with multiple Mongo_Embeddeds
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function setEmbedded($embedded, $data, $save = true)
    {
        if (!$embeddedInfo = $this->getRepository()->getEmbedded($embedded)) {
            throw new Exception('Unknown embedded "'.$embedded.'" for model '.get_class($this));
        }
        $className = $embeddedInfo[0];
        if (!empty($embeddedInfo[3])) {
            if (is_object($data) && $data instanceof Mongo_Embedded) {
                $this->{$embeddedInfo[1]} = $data->getData();
            } else {
                $this->{$embeddedInfo[1]} = $data;
            }
            if ($save !== null) {
                if ($this->getCollection()->update(array('_id' => $this->_id), array('$set' => array($embeddedInfo[1] => $data)), array('safe' => $save))) {
                    $this->setDatabaseProperty($embeddedInfo[1], $data);
                    return true;
                }
                return false;
            }
            return true;
        } else {
            if (!is_array($data) || !isset($data[0])) {
                $data = array($data);
            }
            $set = array();
            $pushAll = array();
            $currentEntries = (array) $this->{$embeddedInfo[1]};
            foreach ($data as $entry) {
                if (is_object($entry) && $entry instanceof Mongo_Embedded) {
                    $entry = $entry->getData();
                }
                if (empty($entry[$embeddedInfo[2]])) {
                    $entry[$embeddedInfo[2]] = $this->generateEmbeddedKey($currentEntries, $embeddedInfo[2]);
                    $currentEntries[] = $entry;
                    $pushAll[] = $entry;
                } else {
                    $found = false;
                    foreach ($currentEntries as $key => $value) {
                        if ($value[$embeddedInfo[2]] == $entry[$embeddedInfo[2]]) {
                            $currentEntries[$key] = $value;
                            $set[$key] = $value;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $currentEntries[] = $entry;
                        $pushAll[] = $entry;
                    }
                }
            }
            $this->{$embeddedInfo[1]} = $currentEntries;
            $query = array();
            if (count($pushAll)) {
                $query['$pushAll'] = array($embeddedInfo[1] => $pushAll);
            }
            if (count($set)) {
                $dbSet = array();
                foreach ($set as $k => $v) {
                    $dbSet[$embeddedInfo[1].'.'.$k] = $v;
                }
                $query['$set'] = $dbSet;
            }
            if ($save !== null) {
                if ($this->getCollection()->update(array('_id' => $this->_id), $query, array('safe' => $save))) {
                    $dbValues = (array) $this->getDatabaseProperty($embeddedInfo[1]);
                    foreach ($pushAll as $entry) {
                        $dbValues[] = $entry;
                    }
                    foreach ($set as $k => $v) {
                        $dbValues[$k] = $v;
                    }
                    $this->setDatabaseProperty($embeddedInfo[1], $dbValues);
                    return true;
                }
                return false;
            }
            return true;
        }
    }

    /**
     * removes the chosen Mongo_Embeddeds (or all for $key = true) from the embedded list
     *
     * @param string $embedded the embedded name
     * @param mixed $key one or more keys for Mongo_Embedded objects or true to remove all
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function removeEmbedded($embedded, $key = true, $save = true)
    {
        if (!$embeddedInfo = $this->getRepository()->getEmbedded($embedded)) {
            throw new Exception('Unknown embedded "'.$embedded.'" for model '.get_class($this));
        }
        if ($key === true) {
             $this->{$embeddedInfo[1]} = array();
             if ($save !== null) {
                if ($this->getCollection()->update(array('_id' => $this->_id), array('$set' => array($embeddedInfo[1] => array())), array('safe' => $save))) {
                    $this->setDatabaseProperty($embeddedInfo[1], array());
                    return true;
                }
                return false;
            }
            return true;
        } else {
            if (!is_array($key)) {
                $key = array($key);
            }
            $unset = false;
            $currentData = (array) $this->{$embeddedInfo[1]};
            foreach ($key as $entry) {
                foreach ($currentData as $currentKey => $value) {
                    if ($value[$embeddedInfo[2]] == $entry) {
                        $unset=true;
                        unset($currentData[$currentKey]);
                        break;
                    }
                }
            }
            if (!$unset) {
                return true;
            }
            $this->{$embeddedInfo[1]} = array_values($currentData);
            if ($save !== null) {
                return $this->save($save);
            }
            return true;
        }
    }

    protected function generateEmbeddedKey($list, $key)
    {
        $newKey = 1;
        foreach ((array) $list as $current) {
            if ((int) $current[$key] >= $newKey) {
                $newKey = ((int) $current[$key]) + 1;
            }
        }
        return $newKey;
    }

    /**
     *
     * @param bool|integer $safe @see php.net/manual/en/mongocollection.update.php
     * @return bool Returns if the update was successfully sent to the database.
     */
    public function save($safe = true)
    {
        try {
            return $this->getRepository()->save($this, $safe);
        } catch (MongoException $e) {
            throw $e;
            return false;
        }
    }

    /**
     *
     * @param bool|integer $safe @see php.net/manual/en/mongocollection.remove.php
     * @return mixed If "safe" is set, returns an associative array with the status of the remove ("ok"), the number of items removed ("n"), and any error that may have occured ("err"). Otherwise, returns TRUE if the remove was successfully sent, FALSE otherwise.
     */
    public function remove($safe = true)
    {
        try {
            return $this->getRepository()->remove($this, $safe);
        } catch (MongoException $e) {
            throw $e;
            return false;
        }
    }

    public function preSave()
    {

    }

    public function preRemove()
    {
        
    }

    public function postCreate()
    {

    }

    public function postLoad()
    {

    }
}