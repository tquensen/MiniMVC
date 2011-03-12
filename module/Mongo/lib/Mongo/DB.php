<?php
/**
 * Mongo_DB is responsible for mongodb connections
 */
class Mongo_DB
{
    protected $connections = array();
    protected $currentConnection = 'mongo';

    /**
     *
     * @return null
     */
    public function init($connection = 'mongo')
    {
        if (!$connection) {
            return;
        }

        $this->currentConnection = $connection;

        $dbSettings = MiniMVC_Registry::getInstance()->settings->get('db');
        if (!$dbSettings || !isset($dbSettings[$connection])) {
            return;
        }

        if (!empty($dbSettings[$connection]['server'])) {
            $mongo = new Mongo($dbSettings[$connection]['server'], !empty($dbSettings[$connection]['options']) ? $dbSettings[$connection]['options'] : array());
        } else {
            $mongo = new Mongo();
        }
        $database = !empty($dbSettings[$connection]['database']) ? $dbSettings[$connection]['database'] : $connection;
        $this->connections[$connection] = $mongo->$database;
    }

    /**
     *
     * @return MongoDB
     */
    public function get($connection = null)
    {
        if (!$connection) {
            $connection = $this->currentConnection;
        }
        if (!isset($this->connections[$connection])) {
            $this->init($connection);
        }
        return isset($this->connections[$connection]) ? $this->connections[$connection] : null;
    }

    public function setConnection($connection = 'default')
    {
        $this->currentConnection = $connection;
    }


    public function finalizeRouteEvent($event, $routeData)
    {
        if (isset($routeData['mongo']) && is_array($routeData['mongo'])) {
            if (isset($routeData['mongo'][0]) && !is_array($routeData['mongo'][0])) {
                $routeData['mongo'] = array($routeData['mongo']);
            }
            $models = array();
            foreach ($routeData['mongo'] as $modelKey => $modelData) {
                if (!empty($routeData['parameter']['_mongo'])) {
                    if (is_array($routeData['parameter']['_mongo']) && ($modelKey != 0 && !empty($routeData['parameter']['_mongo'][$modelKey]))) {
                        $models[$modelKey] = $routeData['parameter']['_mongo'][$modelKey];
                        continue;
                    } elseif ($modelKey == 0) {
                        $models[$modelKey] = $routeData['parameter']['_mongo'];
                        continue;
                    }
                }
                $modelName = $modelData[0];
                if (!class_exists($modelName) || !is_subclass_of($modelName, 'Mongo_Model')) {
                    $models[$modelKey] = null;
                } else {
                    $object = new $modelName;
                    $repository = $object->getRepository();
                    $property = !empty($modelData[1]) ? $modelData[1] : '_id';
                    $refProperty = !empty($modelData[2]) ? $modelData[2] : $property;
                    $models[$modelKey] = empty($routeData['parameter'][$refProperty]) ? null : $repository->findOne(array($property => $routeData['parameter'][$refProperty]));
                }
            }
            $routeData['parameter']['mongo'] = (count($models) === 1 && isset($models[0])) ? reset($models) : $models;
        }
        return $routeData;
    }

}