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

        $this->connections[$connection] = new LoggablePDO(
            $dbSettings[$connection]['driver'],
            $dbSettings[$connection]['username'],
            $dbSettings[$connection]['password'],
            isset($dbSettings[$connection]['options']) ? array_merge(array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION), $dbSettings[$connection]['options']) : array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        $this->connections[$connection]->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('LoggablePDOStatement', array($this->connections[$connection])));

        if ($this->connections[$connection]->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
            $this->connections[$connection]->exec('SET CHARACTER SET utf8');
        }

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


class LoggablePDO extends PDO
{
    protected $log = array();

    public function query($query) {
        $start = microtime(true);
        $result = parent::query($query);
        $time = microtime(true) - $start;
        $this->log($query, null, round($time * 1000, 3));
        return $result;
    }

    public function log($query, $params = null, $time = 0)
    {
        $this->log[] = array($query, $params, $time);
    }

    public function showLog()
    {
        $data = '<table border="1"><tr><th>#</th><th>query</th><th>parameters</th><th>time</th></tr>';

        foreach ($this->log as $num => $log) {
            $params = '';
            foreach ((array) $log[1] as $key => $value) {
                $params .= $key.': '.$value.'<br />';
            }
            $data .= '<tr><td>'.($num + 1).'</td><td>'.$log[0].'</td><td>'.$params.'</td><td>'.$log[2].'ms</td></tr>';
        }
        $data .= '</table>';

        return $data;
    }
}

class LoggablePDOStatement extends PDOStatement
{
    /**
     * @var LoggablePDO
     */
    protected $db = null;
    protected $params = array();

    protected function __construct($db)
    {
        $this->db = $db;
    }

    public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR, $length = null, $driver_options = null)
    {
        $this->params[$parameter] = $variable;
        return parent::bindParam($parameter, &$variable, $data_type);
    }

    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        $this->params[$parameter] = $value;
        return parent::bindValue($parameter, $value, $data_type);
    }

    public function execute($input_parameters = null)
    {
        $start = microtime(true);
        if (is_array($input_parameters)) {
            foreach ($input_parameters as $key => $param) {
                $this->params[$key + 1] = $param;
            }
            $result = parent::execute($input_parameters);
        } else {
            $result = parent::execute();
        }
        $time = microtime(true) - $start;
        $this->db->log($this->queryString, $this->params, round($time * 1000, 3));

        return $result;
    }
}