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
        
        $dbSettings = MiniMVC_Registry::getInstance()->settings->get('db');
        if (!$dbSettings || !isset($dbSettings[$connection])) {
            return;
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
        $registry = MiniMVC_Registry::getInstance();
        if ($registry->settings->get('config/modelPathsLoaded')) {
            return;
        }

        $autoloadPaths = $registry->settings->get('config/autoloadPaths', array());

        foreach (array_reverse($registry->settings->get('modules')) as $module) {
            if (!in_array('Module/' . $module . '/Model', $autoloadPaths)) {
                $autoloadPaths[] = 'Module/' . $module . '/Model';
            }
        }
        $registry->settings->set('config/modelPathsLoaded', true);
        $registry->settings->set('config/autoloadPaths', $autoloadPaths);
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

