<?php

class MiniMVC_Query
{
    /**
     *
     * @var PDO
     */
    protected $db = null;
    /**
     *
     * @var PDO
     */
    protected static $database = null;
    protected $type = 'SELECT';
    protected $columns = array();
    protected $from = null;
    protected $join = array();
    protected $set = array();
    protected $where = array();
    protected $order = '';
    protected $group = '';
    protected $limit = null;
    protected $offset = null;
    protected $needPreQuery = false;
    protected $tables = array();
    protected $relations = array();

    public static function setDatabase($db)
    {
        self::$database = $db;
    }

    /**
     *
     * @param PDO $db
     * @return MiniMVC_Query
     */
    public static function create($db = null)
    {
        $query = new MiniMVC_Query($db);
        return $query;
    }

    public function __construct($db = null)
    {
        $this->db = $db ? $db : self::$database;
    }

    /**
     *
     * @param string|null $alias
     * @return MiniMVC_Query
     */
    public function select($columns = null)
    {
        $this->type = 'SELECT';
        $this->columns = array_merge($this->columns, array_map('trim', explode(',', $columns)));
        
        return $this;
    }

    /**
     *
     * @param string|null $alias
     * @return MiniMVC_Query
     */
    public function insert($columns = null)
    {
        $this->type = 'INSERT INTO';
        $this->columns = array_merge($this->columns, array_map('trim', explode(',', $columns)));

        return $this;
    }

    /**
     *
     * @param string|null $alias
     * @return MiniMVC_Query
     */
    public function update($columns = null)
    {
        $this->type = 'UPDATE';
        $this->columns = array_merge($this->columns, array_map('trim', explode(',', $columns)));

        return $this;
    }

    /**
     *
     * @param string|null $alias
     * @return MiniMVC_Query
     */
    public function delete($columns = null)
    {
        $this->type = 'DELETE';
        $this->columns = array_merge($this->columns, array_map('trim', explode(',', $columns)));

        return $this;
    }

    /**
     *
     * @param MiniMVC_Table $table
     * @param string|null $alias
     * @return MiniMVC_Query
     */
    public function from($table, $alias = null)
    {
        if (is_string($table)) {
            if (!class_exists($table.'Table')) {
                $this->from = $table . ' ' . $alias;
                return $this;
            }
            $table = call_user_func($table.'Table'.'::getInstance');
        }
        $this->from = $alias;
        $this->tables[$alias] = $table;
        
        return $this;
    }

    /**
     *
     * @param string $relation
     * @param string $alias
     * @param string $on on condition
     * @param string $type join type
     * @return MiniMVC_Query
     */
    public function join($table, $alias, $relation = null, $type = 'LEFT')
    {
        if (strpos($table, '.')) {
            list($table, $relation) = array_map('trim', explode('.', $table, 2));
        }
        if (!isset($this->tables[$table]) || !$data = $this->tables[$table]->getRelation($relation)) {
            $this->join[$alias] = array($table, $relation, $type);
            return $this;
        }
        $this->tables[$alias] = call_user_func($data[0].'Table'.'::getInstance');
        if (isset($data[3]) && $data[3] && $data[3] !== true) {
            $this->join[$table.'_'.$alias] = array($data[3], $table.'.'.$this->tables[$table]->getIdentifier().' = '.$table.'_'.$alias.'.'.$data[1], $type);
            $this->join[$alias] = array($this->tables[$alias]->getTableName(), $table.'_'.$alias.'.'.$data[2].' = '.$alias.'.'.$this->tables[$alias]->getIdentifier(), $type);
        } else {
            $this->join[$alias] = array($this->tables[$alias]->getTableName(), $table.'.'.$data[1].' = '.$alias.'.'.$data[2], $type);
        }
        $this->relations[] = array($table, $alias, $relation);

        return $this;
    }

    /**
     *
     * @param string $condition
     * @return MiniMVC_Query
     */
    public function where($condition)
    {
        if (trim($condition)) {
            $this->where[] = $condition;
        }
        return $this;
    }

    /**
     *
     * @param string $data
     * @return MiniMVC_Query
     */
    public function set($data)
    {
        if (trim($data)) {
            $this->set[] = $data;
        }
        return $this;
    }

    /**
     *
     * @param string $order
     * @return MiniMVC_Query
     */
    public function orderBy($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     *
     * @param string $group
     * @return MiniMVC_Query
     */
    public function groupBy($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     *
     * @param int $limit
     * @param int $offset
     * @param bool $needPreQuery if the keys of the from table should be queried (true for one-to-many joins)
     * @return MiniMVC_Query
     */
    public function limit($limit, $offset = 0, $needPreQuery = false)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->needPreQuery = $needPreQuery;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get($values = array(), $isPreQuery = false)
    {

        if ($isPreQuery) {
            $q = 'SELECT ';
            $select = array($this->from ? $this->from.'.'.$this->tables[$this->from]->getIdentifier() : $this->tables[$this->from]->getIdentifier());
        } else {
            $q = $this->type.' ';
            $comma = false;
            $select = array();
            if ($this->type == 'SELECT') {
                foreach ($this->columns as $v) {
                    if (isset($this->tables[$v])) {
                        $select[] = $this->_select($v);
                    } else {
                        $select[] = $v;
                    }
                }
            } else {
                $select = $this->columns;
            }
        }
        $q .= implode(', ', $select);
        if ($this->type == 'INSERT INTO' || $this->type == 'UPDATE') {
            $q .= ' SET '.implode(' , ',$this->set);
        } else {
            $q .= $this->_from($this->from);
        }

        foreach ($this->join as $join => $info) {
            $q .= $this->_join($join, $info[0], isset($info[1]) ? $info[1] : null, isset($info[2]) ? $info[2] : null);
        }
        $condition = count($this->where) ? implode(' AND ', $this->where) : '';
        if ($condition) {
            $q .= ' WHERE '.$condition.' ';
        }
        if ($this->limit || $this->offset) {
            if (!$isPreQuery && $this->needPreQuery && isset($this->tables[$this->from])) {
                $limit = '';
                $q .= ($condition ? ' AND ' : ' WHERE ') . $this->_in($this->from, null, $this->_getIdentifiers($values));
            } else {
                $limit = ' LIMIT '.(int)$this->offset.','.(int)$this->limit;
            }
        } else {
            $limit = '';
        }
        if ($isPreQuery) {
             $q .= ' GROUP BY '.($this->from ? $this->from : $this->tables[$this->from]->getTableName()).'.'.$this->tables[$this->from]->getIdentifier().' ';
        } elseif ($this->group) {
            $q .= ' GROUP BY '.$this->group.' ';
        }
        if ($this->order) {
            $q .= ' ORDER BY '.$this->order.' ';
        }
        $q .= $limit;
        return $q;
    }

    protected function count($count = '*', $values = array())
    {
        if (!is_array($values) && $values !== null) {
            $values = array($values);
        }

        $q = 'SELECT COUNT('.$count.')';
        $q .= $this->_from($this->from);
        foreach ($this->join as $join => $info) {
            $q .= $this->_join($join, $info[0], isset($info[1]) ? $info[1] : null, isset($info[2]) ? $info[2] : null);
        }
        $condition = count($this->where) ? implode(' AND ', $this->where) : '';
        if ($condition) {
            $q .= ' WHERE '.$condition.' ';
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        return $stmt->fetchColumn();
    }

    public function execute($values = array(), $query = null)
    {
        if (!is_array($values) && $values !== null) {
            $values = array($values);
        }
        
        $sql = ($query !== null) ? $query : $this->get($values);

        if (!is_array($values) && $values) {
            $values = array($values);
        }

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($values);

        return $result !== false ? $stmt : false;
    }

    public function build($values = array(), $returnAll = false)
    {
        if (!is_array($values) && $values !== null) {
            $values = array($values);
        }

        $sql = $this->get($values); 

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $entries = array();
        if (count($this->tables) === 1) {
            foreach ($results as $row) {
                $entries[] = $this->tables[$this->from]->buildModel($this->_filter($row, $this->from));
            }
            return $entries;
        }
        
        $aliases = array();
        $relations = array();
        $aliasedIdentifiers = array();
        foreach ($this->columns as $a) {
            if (isset($this->tables[$a])) {
                $aliases[$a] = $this->tables[$a];
            }
        }
        foreach ($this->relations as $relation) {
            if (isset($aliases[$relation[0]]) && isset($aliases[$relation[1]])) {
                $relations[] = $relation;
            }
        }
        foreach ($aliases as $alias => $buildClass) {
            $aliasedIdentifiers[$alias] = $alias.'__'.$buildClass->getIdentifier();
        }
        foreach ($results as $row) {
            foreach ($aliases as $alias => $buildClass) {
                if ($row[$aliasedIdentifiers[$alias]] && !isset($entries[$alias][$row[$aliasedIdentifiers[$alias]]])) {
                    $entries[$alias][$row[$aliasedIdentifiers[$alias]]] = $buildClass->buildModel($this->_filter($row, $alias));
                }
            }
            foreach ($relations as $relation) {
                if ($row[$aliasedIdentifiers[$relation[0]]] && $row[$aliasedIdentifiers[$relation[1]]]) {
                    $entries[$relation[0]][$row[$aliasedIdentifiers[$relation[0]]]]->{'set'.$relation[2]}($entries[$relation[1]][$row[$aliasedIdentifiers[$relation[1]]]], false);
                }
            }
        }

        return $returnAll ? $entries : (reset($entries) !== false ? reset($entries) : array());
    }

    public function buildArray($values = array(), $returnAll = false)
    {
        if (!is_array($values) && $values !== null) {
            $values = array($values);
        }

        $sql = $this->get($values);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $entries = array();
        if (count($this->tables) === 1) {
            foreach ($results as $row) {
                $entries[] = $this->_filter($row, $this->from);
            }
            return $entries;
        }

        $aliases = array();
        $relations = array();
        $aliasedIdentifiers = array();
        foreach ($this->columns as $a) {
            if (isset($this->tables[$a])) {
                $aliases[$a] = $this->tables[$a];
            }
        }
        foreach ($this->relations as $relation) {
            if (isset($aliases[$relation[0]]) && isset($aliases[$relation[1]])) {
                $relations[] = $relation;
            }
        }
        foreach ($aliases as $alias => $buildClass) {
            $aliasedIdentifiers[$alias] = $alias.'__'.$buildClass->getIdentifier();
        }
        foreach ($results as $row) {
            foreach ($aliases as $alias => $buildClass) {
                if ($row[$aliasedIdentifiers[$alias]] && !isset($entries[$alias][$row[$aliasedIdentifiers[$alias]]])) {
                    $entries[$alias][$row[$aliasedIdentifiers[$alias]]] = $this->_filter($row, $alias);
                }
            }
            foreach ($relations as $relation) {
                if ($row[$aliasedIdentifiers[$relation[0]]] && $row[$aliasedIdentifiers[$relation[1]]] && !isset($entries[$relation[0]][$row[$aliasedIdentifiers[$relation[0]]]][$relation[2]][$row[$aliasedIdentifiers[$relation[1]]]])) {
                    $entries[$relation[0]][$row[$aliasedIdentifiers[$relation[0]]]][$relation[2]][$row[$aliasedIdentifiers[$relation[1]]]] = $entries[$relation[1]][$row[$aliasedIdentifiers[$relation[1]]]];
                }
            }
        }

        return $returnAll ? $entries : (reset($entries) !== false ? reset($entries) : array());
    }

    protected function _filter($row, $alias = '')
    {
        if (!$alias) {
            return $row;
        }
        $prefix = $alias.'__';
        $length = strlen($prefix);
        foreach ($row as $k=>$v) {
            if (substr($k, 0, $length) == $prefix) {
               $row[substr($k, $length)] = $v;
            }
            unset($row[$k]);
        }
        return $row;
    }

    protected function _select($alias = null, $prefix = null)
    {
        $sql = $prefix === true ? ' , ' : $prefix;

        if (!$alias) {
            $sql .= implode(', ', $this->tables[$alias]->getColumns()).' ';
            return $sql;
        }
        $comma = false;
        foreach ($this->tables[$alias]->getColumns() as $column) {
            if ($comma) {
                $sql .= ', ';
            } else {
                $comma = true;
            }
            $sql .= $alias.'.'.$column.' '.$alias.'__'.$column.' ';
        }
        return $sql;
    }

    protected function _join($alias, $table, $on = null, $type = 'LEFT')
    {
        return ' '.$type.' JOIN '.$table.' '.$alias.' '.($on ? 'ON '.$on : '').' ';
    }

    protected function _from($alias = null)
    {
        return ' FROM '.(isset($this->tables[$alias]) ? $this->tables[$alias]->getTableName() : '').' '.$alias.' ';
    }

    protected function _in($alias = null, $key = null, $values = array())
    {
        if (empty($values) || !is_array($values)) {
            return  ' ';
        }
        if (!$key) {
            $key = $this->tables[$alias]->getIdentifier();
        }
        if ($alias) {
            $key = $alias.'.'.$key;
        }
        return ' '.$key.' IN ("'.implode('","',  $values).'") ';
    }

    protected function _getIdentifiers($values = array())
    {
        $sql = $this->get($values, true);
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        $ids = array();
        foreach($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
            $ids[] = $row[0];
        }
        return $ids;
    }
    

}
