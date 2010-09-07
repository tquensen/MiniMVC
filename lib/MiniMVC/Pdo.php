<?php
/**
 * MiniMVC_Pdo is responsible for the current database connection
 */
class MiniMVC_Pdo
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

        $this->connections[$connection] = new PDO(
            $dbSettings[$connection]['driver'],
            $dbSettings[$connection]['username'],
            $dbSettings[$connection]['password'],
            isset($dbSettings[$connection]['options']) ? array_merge(array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION), $dbSettings[$connection]['options']) : array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        MiniMVC_Query::setDatabase($this->get());

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

        foreach (array_reverse($registry->settings->get('modules', array())) as $module) {
            if (!in_array(MODULEPATH . $module . '/model', $autoloadPaths)) {
                $autoloadPaths[] = MODULEPATH . $module . '/model';
            }
        }
        $registry->settings->set('config/modelPathsLoaded', true);
        $registry->settings->set('config/autoloadPaths', $autoloadPaths);
    }

    /**
     *
     * @return PDO
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

