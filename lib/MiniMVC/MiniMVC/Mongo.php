<?php
/**
 * MiniMVC_Mongo is responsible for mongodb connections
 */
class MiniMVC_Mongo
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

        $this->connections[$connection] = is_string($dbSettings[$connection]) ? new Mongo($dbSettings[$connection]) : new Mongo();
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
        $this->connections = $connection;
    }

}