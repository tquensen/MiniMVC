<?php
/**
 * @method {name} create() create($data = array()) creates a new {name} object
 * @method {name} findOne() findOne($query) returns the first matching {name} object
 */
class {name}Repository extends Mongo_Repository
{
    protected $collectionName = '{table}';
    protected $className = '{name}';
    protected $autoId = false;
    protected $columns = array('_id', 'value');
    protected $relations = array({relations_list});
    protected $embedded = array({embedded_list});

    /**
     * initiate the collection for this model
     */
    public function install($installedVersion = 0, $targetVersion = 0)
    {
        switch ($installedVersion) {
            case 0:
                //$this->getCollection()->ensureIndex(array('value' => 1), array('safe' => true, 'unique' => true));
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
     * @return {name}Repository
     */
    public static function get($connection = null)
    {
        return new self(null, null, $connection);
    }
}
