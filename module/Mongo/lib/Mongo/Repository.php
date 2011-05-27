<?php
/**
 * Mongo_Repository is the base class for mongodb model collections
 */
class Mongo_Repository
{

    /**
     *
     * @var MongoDB
     */
    protected $db = null;
    protected $collectionName = null;
    protected $className = null;
    protected $autoId = false;
    protected $columns = array();
    protected $relations = array();
    protected $embedded = array();

    protected $connection = null;

    public function __construct($collectionName = null, $className = null, $connection = null)
    {
        $this->connection = $connection;
        $this->db = MiniMVC_Registry::getInstance()->mongo->get($this->connection);

        if ($collectionName) {
            $this->collectionName = $collectionName;
        } elseif (!$this->collectionName) {
            $this->collectionName = str_replace('Repository', '', get_class($this));
        }

        if ($className) {
            $this->className = $className;
        } elseif (!$this->className) {
            $this->className = str_replace('Repository', '', get_class($this));
        }
    }

    /**
     *
     * @return MongoDB
     */
    public function getDB()
    {
        return $this->db;
    }

    /**
     *
     * @return MongoCollection
     */
    public function getCollection()
    {
        return $this->getDB()->{$this->collectionName};
    }
    
    /**
     *
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collectionName;
    }

    /**
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }    

    /**
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param string $relation the relation name
     * @return array
     */
    public function getRelation($relation)
    {
        return isset($this->relations[$relation]) ? $this->relations[$relation] : null;
    }

    /**
     *
     * @return array
     */
    public function getEmbeddeds()
    {
        return $this->embedded;
    }

    /**
     * @param string $embedded the embedded name
     * @return array
     */
    public function getEmbedded($embedded)
    {
        return isset($this->embedded[$embedded]) ? $this->embedded[$embedded] : null;
    }

    /**
     *
     * @param array $data initial data for the model
     * @param bool $isNew whether this is a new object (true, default) or loaded from database (false)
     * @return Mongo_Model
     */
    public function create($data = array(), $isNew = true)
    {
        $name = $this->className;
        $model = new $name($data, null, $this);
        $isNew ? $model->postCreate() : $model->postLoad();
        return $model;
    }

    /**
     *
     * @param MongoCursor|array $data initial data for the models
     * @param bool $single set to true if $data is a single model
     * @return array
     */
    public function build($data = array(), $single = false)
    {
        $return = array();
        if ($single) {
            $data = array($data);
        }
        foreach ($data as $current) {
            $model = $this->create($current);
            if ($columns = $this->getColumns()) {
                foreach ($this->getColumns() as $col) {
                    if (isset($current[$col]))
                    $model->setDatabaseProperty($col, $current[$col]);
                }
            } else {
                foreach ($current as $col => $colValue) {
                    $model->setDatabaseProperty($col, $colValue);
                }
            }
            if (is_array($model->_id)) {
                $return[] = $model;
            } else {
                $return[(string) $model->_id] = $model;
            }
        }
        return $single ? reset($return) : $return;
    }

    /**
     *
     * @param mixed $query an array of fields for which to search or the _id as string or MongoId
     * @param bool $build (default true) set to false to return the raw MongoCursor
     * @return Mongo_Model the matching record or null
     */
    public function findOne($query, $build = true)
    {
        if (is_string($query)) {
            $query = array('_id' => $this->autoId ? new MongoId($query) : $query);
        } elseif ($query instanceof MongoId) {
            $query = array('_id' => $query);
        }
        $data = $this->getCollection()->findOne($query);
        if ($data) {
            return $this->build($data, true);
        }
    }

    /**
     *
     * @param array $query Associative array or object with fields to match.
     * @param int $limit Specifies an upper limit to the number returned.
     * @param int $skip Specifies a number of results to skip before starting the count.
     * @return int Returns the number of documents matching the query.
     */
    public function count($query = array(), $limit = null, $skip = null)
    {
        return $this->getCollection()->count($query, $limit, $skip);
    }

    /**
     *
     * @param array $sort The fields by which to sort.
     * @param bool $build (default true) set to false to return the raw MongoCursor
     * @return MongoCursor|array Returns an array or a cursor for the search results.
     */
    public function findAll($sort = array(), $build = true)
    {
        return $this->find(array(), array(), $sort, null, null, $build);
    }

    /**
     *
     * @param array $query The fields for which to search.
     * @param array $sort The fields by which to sort.
     * @param int $limit The number of results to return.
     * @param int $skip The number of results to skip.
     * @param bool $build (default true) set to false to return the raw MongoCursor
     * @return MongoCursor|array Returns an array or a cursor for the search results.
     */
    public function find($query = array(), $sort = array(), $limit = null, $skip = null, $build = true)
    {
        $cursor = $query ? $this->getCollection()->find($query) : $this->getCollection()->find();
        if ($sort) {
            $cursor->sort($sort);
        }
        if ($limit) {
            $cursor->limit($limit);
        }
        if ($skip) {
            $cursor->skip($skip);
        }

        return $build ? $this->build($cursor) : $cursor;
    }
    
    /**
     * creates a Mongo_MapReduce object for this collection
     * 
     * @param MongoCode|string $map the map function as MongoCode or string
     * @param MongoCode|string $reduce the reduce function as MongoCode or string
     * @param MongoCode|string $finalize the finalize function as MongoCode or string
     * @return Mongo_MapReduce 
     */
    public function mapReduce($map = null, $reduce = null, $finalize = null)
    {
        return new Mongo_MapReduce($this->collectionName, $this->connection, $map, $reduce, $finalize);
    }
    
    /**
     *
     * @param array $query The fields for which to filter.
     * @param bool $justOne Remove at most one record matching this criteria.
     * @param bool|integer $safe @see php.net/manual/en/mongocollection.remove.php
     * @param bool $raw true to remove the entries directly, false (default) to call remove() on each model 
     * @return bool if $raw is true, returns the status of the query. If $raw is false, it returns always true
     */
    public function removeBy($query = array(), $justOne = false, $safe = true, $raw = false)
    {
        if ($raw) {
            $options = array();
            if ($justOne) {
                $options['justOne'] = true;
            }
            if ($safe) {
                $options['safe'] = $safe;
            }
            return $this->getCollection()->remove($query);
        } else {
            foreach($this->find($query, array(), $justOne ? 1 : null, null) as $model) {
                $model->remove($safe);
            } 
            return true;
        }
    }

    /**
     *
     * @param Mongo_Model $model the model to save
     * @param bool|integer $safe @see php.net/manual/en/mongocollection.update.php
     * @return bool Returns if the update was successfully sent to the database.
     */
    public function save($model, $safe = true)
    {
        try {
            if ($model->preSave() === false) {
                return false;
            }
            $data = $model->getData();
            if ($model->isNew()) {
                if ($columns = $this->getColumns()) {
                    $insert = array();
                    foreach ($columns as $column) {
                        if ($model->$column !== null) {
                            $insert[$column] = $model->$column;
                        }
                    }
                } else {
                    $insert = $model->getData();
                }
                $status = $this->getCollection()->insert($insert, array('safe' => $safe));
                if ($status) {
                    if ($this->autoId) {
                        $model->_id = $insert['_id'];
                    }
                    foreach ($insert as $key => $value) {
                        $model->setDatabaseProperty($key, $value);
                    }
                    return true;
                }
                return false;
            } else {
                $query = array();
                if ($columns = $this->getColumns()) {
                    foreach ($columns as $column) {
                        if ($model->$column !== $model->getDatabaseProperty($column)) {
                            if ($model->$column === null) {
                                $query['$unset'][$column] = 1;
                            } else {
                                $query['$set'][$column] = $model->$column;
                            }
                        }
                    }
                    if (!count($query)) {
                        return true;
                    }
                } else {
                    $query = $model->getData();
                }
                $status = $this->getCollection()->update(array('_id' => $model->_id), $query, array('safe' => $safe));
                if ($status) {
                    if ($columns) {
                        if (!empty($query['$set'])) {
                            foreach ($query['$set'] as $key => $value) {
                                $model->setDatabaseProperty($key, $value);
                            }
                        }
                        if (!empty($query['$unset'])) {
                            foreach ($query['$unset'] as $key => $dummy) {
                                $model->setDatabaseProperty($key, null);
                            }
                        }
                    } else {
                        foreach ($query as $column => $columnVal) {
                            $model->setDatabaseProperty($column, $columnVal);
                        }
                    }
                    return true;
                }
                return false;
            }
        } catch (Exception $e) {
            //var_dump($e->getMessage());
            throw $e;
        }
    }

    /**
     *
     * @param Mongo_Model $model the model to remove
     * @param bool|integer $safe @see php.net/manual/en/mongocollection.remove.php
     * @return mixed If "safe" is set, returns an associative array with the status of the remove ("ok"), the number of items removed ("n"), and any error that may have occured ("err"). Otherwise, returns TRUE if the remove was successfully sent, FALSE otherwise.
     */
    public function remove($model, $safe = true)
    {
        if (!$model->_id) {
            return false;
        }
        try {
            if ($model->preRemove() === false) {
                return false;
            }
            $status = $this->getCollection()->remove(array('_id' => $model->_id), array('safe' => $safe !== null ? $safe : false));
            if ($status) {
                $model->clearDatabaseProperties();
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function generateSlug($entry, $source, $field, $maxlength = 255)
    {
        $baseslug = MiniMVC_Registry::getInstance()->helper->text->sanitize($source, true);
        $id = $entry->_id;
        $num = 0;
        $slug = $baseslug;
        do {
            if (mb_strlen($slug, 'UTF-8') > $maxlength) {
                $baseslug = mb_substr($baseslug, 0, $maxlength - strlen((string) $num), 'UTF-8');
                $slug = $baseslug . $num;
            }

            $result = $this->getCollection()->findOne(array($field => $slug), array('_id'));
            $num--;
        } while($result && (string)$result['_id'] != (string)$id && $slug = $baseslug . $num);
        return $slug;
    }

    /**
     *
     * @param string $connection the database connection to use (null for the default connection)
     * @return Mongo_Repository
     */
    public static function get($connection = null)
    {
        return new self(null, null, $connection);
    }

}