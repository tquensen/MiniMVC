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
    protected $columns = array();

    protected $connection = null;

    public function __construct($collectionName = null, $connection = null)
    {
        $this->connection = $connection;
        $this->db = $this->registry->mongo->get($this->connection);

        if ($collectionName) {
            $this->collectionName = $collectionName;
        } elseif (!$this->collectionName) {
            $this->collectionName = str_replace('Repository', '', get_class($this));
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
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     *
     * @param array $data initial data for the model
     * @param bool $isNew whether this is a new object (true, default) or loaded from database (false)
     * @return Mongo_Model
     */
    public function create($data = array(), $isNew = true)
    {
        $name = $this->collectionName;
        $model = new $name($data);
        $isNew ? $model->postCreate() : $model->postLoad();
        return $model;
    }

    /**
     *
     * @param MongoCursor|array $data initial data for the models
     * @return array
     */
    public function build($data = array())
    {
        $return = array();
        foreach ($data as $current) {
            $model = $this->create($current);
            foreach ($this->getColumns() as $col) {
                if (isset($current[$col]))
                $model->setDatabaseProperty($col, $current);
            }
            $return[(string) $model->_id] = $model;
        }
        return $return;
    }

    /**
     *
     * @param mixed $query an array of fields for which to search or the _id as string or MongoId
     * @return Mongo_Model the matching record or null
     */
    public function findOne($query)
    {
        if (is_string($query)) {
            $query = array('_id' => $this->autoId ? new MongoId($query) : $query);
        } elseif ($query instanceof MongoId) {
            $query = array('_id' => $query);
        }
        $data = $this->getCollection()->findOne($query);
        if ($data) {
            return $this->create($data);
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
     *
     * @param Mongo_Model $model the model to save
     * @param bool|integer $save @see php.net/manual/en/mongocollection.update.php
     * @return bool Returns if the update was successfully sent to the database.
     */
    public function save($model, $save = true)
    {
        try {
            if ($model->preSave() === false) {
                return false;
            }
            $data = &$model->getData();
            if ($model->isNew()) {
                $insert = array();
                foreach ($this->columns as $column) {
                    $insert[$column] = $model->$column;
                }
                $status = $this->getCollection()->insert($insert, array('save' => $save));
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
                $update = array();
                foreach ($this->columns as $column) {
                    if ($model->$column != $model->getDatabaseProperty($column)) {
                        $update[$column] = $model->$column;
                    }
                }
                if (!count($update)) {
                    return true;
                }
                $status = $this->getCollection()->update(array('_id' => $model->_id), array('$set' => $update), array('save' => $save));
                if ($status) {
                    foreach ($update as $key => $value) {
                        $model->setDatabaseProperty($key, $value);
                    }
                    return true;
                }
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *
     * @param Mongo_Model $model the model to save
     * @param bool|integer $save @see php.net/manual/en/mongocollection.remove.php
     * @return mixed If "safe" is set, returns an associative array with the status of the remove ("ok"), the number of items removed ("n"), and any error that may have occured ("err"). Otherwise, returns TRUE if the remove was successfully sent, FALSE otherwise.
     */
    public function remove($model, $save = true)
    {
        if (!$model->_id) {
            return false;
        }
        try {
            if ($model->preRemove() === false) {
                return false;
            }
            $status = $this->getCollection()->remove(array('_id' => $model->_id), array('save' => $save));
            if ($status) {
                $model->clearDatabaseProperties();
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
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
     * @return Mongo_Repository
     */
    public static function get($collectionName, $connection = null)
    {
        return new self($collectionName, $connection);
    }

}