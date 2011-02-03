<?php
/**
 * Mongo_Model is the base class for mongodb models
 *
 * @property MongoId $_id
 *
 */
class Mongo_Model
{

    protected $_properties = array();
    protected $_databaseProperties = array();
    /**
     *
     * @var Mongo_Repository
     */
    protected $_repository = null;

    public function __construct($data = array(), $connection = null)
    {
        $this->_properties = $data;
        $repositoryName = get_class($this).'Repository';
        $this->_repository = class_exists($repositoryName) ? new $repositoryName(null, $connection) : new Mongo_Repository(get_class($this), $connection);
    }

    public function __get($key)
    {
        return isset($this->_properties[$key]) ? $this->_properties[$key] : null;
    }

    public function __set($key, $value)
    {
        $this->_properties[$key] = $value;
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

    /**
     *
     * @return Mongo_Repository
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     *
     * @return MongoDB
     */
    public function getDB()
    {
        return $this->_repository->getDB();
    }

    /**
     *
     * @return MongoCollection
     */
    public function getCollection()
    {
        return $this->_repository->getCollection();
    }

    /**
     *
     * @param bool|integer $save @see php.net/manual/en/mongocollection.update.php
     * @return bool Returns if the update was successfully sent to the database.
     */
    public function save($save = true)
    {
        return $this->_repository->save($this, $save);
    }

    /**
     *
     * @param bool|integer $save @see php.net/manual/en/mongocollection.remove.php
     * @return mixed If "safe" is set, returns an associative array with the status of the remove ("ok"), the number of items removed ("n"), and any error that may have occured ("err"). Otherwise, returns TRUE if the remove was successfully sent, FALSE otherwise.
     */
    public function remove($save = true)
    {
        return $this->_repository->remove($this, $save);
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