<?php
/**
 * @method MongoSequence create() create($data = array()) creates a new MongoSequence object
 * @method MongoSequence findOne() findOne($query) returns the first matching MongoSequence object
 */
class MongoSequenceRepository extends Mongo_Repository
{
    protected $collectionName = 'mongo_sequence';
    protected $className = 'MongoSequence';
    protected $autoId = false;
    protected $columns = array('_id', 'seq');
    protected $relations = array();
    protected $embedded = array();

    public function getCurrentId($key)
    {
        $model = $this->findOne($key);
        return $model ? $model->seq : 0;
    }
    
    public function generateNextId($key)
    {
        $seq = $this->getDB()->command(array(
            'findAndModify' => $this->collectionName,
            'query' => array('_id' => $key),
            'update' => array('$inc' => array('seq' => 1)),
            'new' => true,
            'upsert' => true
        ));
        
        return $seq['value']['seq'];
    }
    
    public function setId($key, $newId)
    {
        $seq = $this->getDB()->command(array(
            'findAndModify' => $this->collectionName,
            'query' => array('_id' => $key),
            'update' => array('_id' => $key, 'seq' => $newId),
            'new' => true,
            'upsert' => true
        ));
        
        return $seq['value']['seq'];
    }
    
    /**
     * initiate the collection for this model
     */
    public function install($installedVersion = 0, $targetVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                
            case 1:
                if ($targetVersion && $targetVersion <= 1) break;
            /* //for every new version add your code below (including the lines "case NEW_VERSION:" and "if ($targetVersion && $targetVersion <= NEW_VERSION) break;")

                $this->getCollection()->ensureIndex(array('name' => 1), array('safe' => true));

            case 2:
                if ($targetVersion && $targetVersion <= 2) break;
             */
        }
        return true;
    }

    /**
     * remove the collection for this model
     */
    public function uninstall($installedVersion = 0, $targetVersion = 0)
    {

        SWITCH ($installedVersion) {
            case 0:
            /* //for every new version add your code directly below "case 0:", beginning with "case NEW_VERSION:" and "if ($targetVersion >= NEW_VERSION) break;"
            case 2:
                if ($targetVersion >= 2) break;
                $c->deleteIndex("name");
             */
            case 1:
                if ($targetVersion >= 1) break;
                $this->getCollection()->drop();
        }
        return true;
    }

    /**
     *
     * @param string $connection the database connection to use (null for the default connection)
     * @return MongoSequenceRepository
     */
    public static function get($connection = null)
    {
        return new self(null, null, $connection);
    }
}
