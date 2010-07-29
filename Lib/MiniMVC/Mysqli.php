<?php
/**
 * MiniMVC_Mysqli is responsible for the current database connection
 */
class MiniMVC_Mysqli
{
    protected $connections = array();
    protected $currentConnection = '';

    /**
     *
     * @return null
     */
    public function init($connection = 'default')
    {
        if (!$connection) {
            return;
        }

        $this->currentConnection = $connection;
        
        $dbSettings = MiniMVC_Registry::getInstance()->settings->db;
        if (!$dbSettings || !isset($dbSettings[$connection])) {
            throw new Exception('No database config found for connection "'.$connection.'"!');
        }

        $this->connections[$connection] = @new mysqli(
                $dbSettings[$connection]['host'],
                $dbSettings[$connection]['username'],
                $dbSettings[$connection]['password'],
                $dbSettings[$connection]['database'],
                isset($dbSettings[$connection]['port']) ? $dbSettings[$connection]['port'] : null,
                isset($dbSettings[$connection]['socket']) ? $dbSettings[$connection]['socket'] : null
        );
        
        if(mysqli_connect_errno())
        {
            throw new Exception('Could not connect to database: ' .  mysqli_connect_error());
        }

        $this->registerModels();
    }

    /**
     *
     * @return null
     */
    protected function registerModels()
    {
        $config = MiniMVC_Registry::getInstance()->settings->config;

        if (isset($config['modelPathsLoaded']) && $config['modelPathsLoaded']) {
            return;
        }

        foreach (array_reverse(MiniMVC_Registry::getInstance()->settings->modules) as $module) {
            if (!in_array('Module/' . $module . '/Model', $config['autoloadPaths'])) {
                $config['autoloadPaths'][] = 'Module/' . $module . '/Model';
            }
        }
        $config['modelPathsLoaded'] = true;
        MiniMVC_Registry::getInstance()->settings->saveToCache('config', $config);
    }

    /**
     *
     * @return mysqli
     */
    public function get($connection = null)
    {
        if (!$connection) {
            $connection = $this->currentConnection;
        }
        return isset($this->connections[$connection]) ? $this->connections[$connection] : null;
    }

    public function setConnection($connection = 'default')
    {
        $this->connections = $connection;
    }

}

