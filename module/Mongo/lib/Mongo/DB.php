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

}