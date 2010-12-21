<?php
/**
 * MiniMVC_Pdo is responsible for the current database connection
 */
class MiniMVC_Pdo
{
    protected $connections = array();
    protected $currentConnection = 'default';
    /**
     *
     * @var MiniMVC_Query
     */
    protected $queryClass = '';

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

        $this->connections[$connection] = new MiniMVCPDO(
            $dbSettings[$connection]['driver'],
            $dbSettings[$connection]['username'],
            $dbSettings[$connection]['password'],
            isset($dbSettings[$connection]['options']) ? array_merge(array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION), $dbSettings[$connection]['options']) : array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        $this->connections[$connection]->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('MiniMVCPDOStatement', array($this->connections[$connection])));

        if ($this->connections[$connection]->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
            $this->connections[$connection]->exec('SET CHARACTER SET utf8');
        }

        $queryClass = MiniMVC_Registry::getInstance()->settings->get('config/classes/query', 'MiniMVC_Query');
//        call_user_func(array($queryClass, 'setDatabase'), $this->get());

        $this->queryClass = $queryClass;
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
        if (!isset($this->connections[$connection])) {
            $this->init($connection);
        }
        return isset($this->connections[$connection]) ? $this->connections[$connection] : null;
    }

    /**
     *
     * @return MiniMVC_Query
     */
    public function query($connection = null)
    {
        return new $this->queryClass($this->get($connection));
    }

    public function setConnection($connection = 'default')
    {
        $this->connections = $connection;
    }

}


class MiniMVCPDO extends PDO
{
    protected $log = array();

    protected $transactionDeep = 0;

    public function query($query) {
        $this->log($query);
        $start = microtime(true);
        $result = parent::query($query);
        $time = microtime(true) - $start;
        $this->updateLogTime(round($time * 1000, 3));
        return $result;
    }

    public function log($query, $params = null, $time = 0)
    {
        $this->log[] = array($query, $params, $time);
    }

    public function updateLogTime($time)
    {
        $this->log[count($this->log)-1][2] = $time;
    }

    public function showLog()
    {
        $data = '<table border="1"><tr><th>#</th><th>query</th><th>parameters</th><th>time</th></tr>';
        $time = 0;
        foreach ($this->log as $num => $log) {
            $params = '';
            foreach ((array) $log[1] as $key => $value) {
                $params .= $key.': '.$value.'<br />';
            }
            $data .= '<tr><td>'.($num + 1).'</td><td>'.$log[0].'</td><td>'.$params.'</td><td>'.$log[2].'ms</td></tr>';
            $time += $log[2];
        }
        $data .= '<tr><th>=</th><td colspan="2"></td><th>'.round($time / 1000, 3).'s</th></tr>';
        $data .= '</table>';

        return $data;
    }

    public function beginTransaction()
    {

        if ($this->transactionDeep === 0) {
            $status  = parent::beginTransaction();
            if ($status) {
                $this->transactionDeep++;
            }
            return $status;
        }
        $this->transactionDeep++;
        return true;
    }

    public function commit()
    {
        if ($this->transactionDeep === 1) {
            $status = parent::commit();
            if ($status) {
                $this->transactionDeep--;
            }
            return $status;
        }
        $this->transactionDeep--;
        return true;
    }

    public function rollBack()
    {
        if ($this->transactionDeep === 1) {
            $status = parent::rollBack();
            if ($status) {
                $this->transactionDeep--;
            }
            return $status;
        }
        $this->transactionDeep--;
        return false;
    }

}

class MiniMVCPDOStatement extends PDOStatement
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
        return parent::bindParam($parameter, $variable, $data_type);
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
            $this->db->log($this->queryString, $this->params);
            $result = parent::execute($input_parameters);
        } else {
            $this->db->log($this->queryString, $this->params);
            $result = parent::execute();
        }
        $time = microtime(true) - $start;
        $this->db->updateLogTime(round($time * 1000, 3));

        return $result;
    }
}