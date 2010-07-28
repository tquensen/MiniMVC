<?php
/**
 * MiniMVC_Db is responsible for the current database connection
 */
class MiniMVC_Doctrine
{
    protected $connection = false;
    protected $profiler = false;

    /**
     *
     * @return null
     */
    public function init()
    {
        $dbSettings = MiniMVC_Registry::getInstance()->settings->db;
        if (!$dbSettings) {
            return;
            //throw new Exception('could not connect to database!');
        }

        spl_autoload_register(array('Doctrine', 'autoload'));

        /*
        $cacheConn = Doctrine_Manager::connection(new PDO('sqlite::memory:'));
        $cacheDriver = new Doctrine_Cache_Db(array('connection' => $cacheConn, 'tableName' => $dbSettings['prefix'] . '_cache'));
        $cacheDriver->createTable();
        var_dump($cacheDriver->_getCacheKeys());
        */


        $this->connection = Doctrine_Manager::connection($dbSettings['dbtype'] . '://' . $dbSettings['username'] . ':' . $dbSettings['password'] . '@' . $dbSettings['host'] . '/' . $dbSettings['database']);

        $this->connection->setAttribute(Doctrine_Core::ATTR_DEFAULT_TABLE_CHARSET, 'utf8');
        $this->connection->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT, $dbSettings['prefix'] . '%s');
        //self::$connection->setAttribute(Doctrine_Core::ATTR_TABLE_CLASS, 'QTable');
        //self::$connection->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'QQuery');
        //self::$connection->setAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS, 'QCollection');
        $this->connection->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $this->connection->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        $this->connection->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

        $event = new sfEvent($this, 'minimvc.db.init', array('connection' => $this->connection, 'settings' => $dbSettings));
        MiniMVC_Registry::getInstance()->events->notify($event);

        $this->profiler = new Doctrine_Connection_Profiler();

        $this->connection->setListener($this->profiler);

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
            if (!in_array('Module/' . $module . '/Model/Base', $config['autoloadPaths'])) {
                $config['autoloadPaths'][] = 'Module/' . $module . '/Model/Base';
            }
            if (!in_array('Module/' . $module . '/Model', $config['autoloadPaths'])) {
                $config['autoloadPaths'][] = 'Module/' . $module . '/Model';
            }
        }
        $config['modelPathsLoaded'] = true;
        MiniMVC_Registry::getInstance()->settings->saveToCache('config', $config);
    }

    /**
     *
     * @return Doctrine_Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    public function showLog()
    {
        $time = 0;
        echo '<pre>';
        foreach ($this->profiler as $event) {
            $time += $event->getElapsedSecs();
            echo $event->getName() . " " . sprintf("%f", $event->getElapsedSecs()) . "\n";
            echo $event->getQuery() . "\n";
            $params = $event->getParams();
            if (!empty($params)) {
                print_r($params);
            }
        }
        echo "Total time: " . $time . "\n";
        echo '</pre>';
    }

}

