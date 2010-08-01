<?php

class MiniMVC_Query
{
    /**
     *
     * @var PDO
     */
    protected $db = null;
    protected $type = 'select';
    protected $select = array();
    protected $from = null;
    protected $join = array();
    protected $where = array();
    protected $order = '';
    protected $group = '';
    protected $limit = null;
    protected $offset = null;
    protected $needPreQuery = false;
    protected $tables = array();
    protected $relations = array();

    public function __construct($db = null)
    {
        $this->db = $db ? $db : MiniMVC_Registry::getInstance()->db->get();
    }

    /**
     *
     * @param string|null $alias
     * @return MiniMVC_Query
     */
    public function select($alias = null)
    {
        $this->select[] = $alias;

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
            $table = call_user_func($table.'Table'.'::getInstance');
        }
        $this->from = $alias;
        $this->tables[$alias] = $table;
        
        return $this;
    }

    /**
     *
     * @param MiniMVC_Table $table
     * @param string $alias
     * @param string $related related table alias
     * @param string $on on condition
     * @param string $type join type
     * @param bool $reverse add reverse relations
     * @return MiniMVC_Query
     */
    public function join($table, $relation, $alias)
    {
        if (!isset($this->tables[$table]) || !$data = $this->tables[$table]->getRelation($relation)) {
            return $this;
        }
        $this->tables[$alias] = call_user_func($data[0].'Table'.'::getInstance');
        $this->join[$alias] = array($table.'.'.$data[1].' = '.$alias.'.'.$data[2], isset($data[3]) ? $data[3] : 'LEFT');
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
        $this->needPreQuery = false;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get($values = array())
    {
        $q = 'SELECT ';
        $comma = false;
        $select = array();
        foreach ($this->select as $k => $v) {
            if (isset($this->tables[$v])) {
                $select[] = $this->_select($v);
            } else {
                $select[] = $v;
            }
        }
        $q .= implode(', ', $select);
        $q .= $this->_from($this->from);

        foreach ($this->join as $join => $info) {
            if (isset($this->tables[$join])) {
                $q .= $this->_join($join, $info[0], $info[1]);
            }
        }
        $condition = count($this->where) ? implode(' AND ', $this->where) : '';
        if ($this->where) {
            $q .= ' WHERE '.$condition.' ';
        }
        if ($this->limit || $this->offset) {
            if ($this->needPreQuery) {
                $limit = '';
                $q .= ($condition ? ' AND ' : ' WHERE ') . $this->_in($this->from, null, $this->_getIdentifiers($this->from, $condition, $values, $this->order, $this->limit, $this->offset));
            } else {
                $limit = ' LIMIT '.(int)$this->offset.','.(int)$this->limit;
            }
        } else {
            $limit = '';
        }
        if ($this->group) {
            $q .= ' GROUP BY '.$this->group.' ';
        }
        if ($this->order) {
            $q .= ' ORDER BY '.$this->order.' ';
        }
        $q .= $limit;

        return $q;
    }

    public function build($values = array(), $returnAll = false)
    {
        $sql = $this->get($values);

        if (!is_array($values) && $values) {
            $values = array($values);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $entries = array();
        if (count($this->tables) === 1) {
            foreach ($results as $row) {
                $entries[] = $this->tables[$this->from]->buildModel($row, $this->from);
            }
            return $entries;
        }
        
        $aliases = array();
        $relations = array();
        $aliasedIdentifiers = array();
        foreach ($this->select as $a) {
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
                    $entries[$alias][$row[$aliasedIdentifiers[$alias]]] = $buildClass->buildModel($row, $alias);
                }
            }
            foreach ($relations as $relation) {
                if ($row[$aliasedIdentifiers[$relation[0]]] && $row[$aliasedIdentifiers[$relation[1]]]) {
                    $entries[$relation[0]][$row[$aliasedIdentifiers[$relation[0]]]]->{'set'.$relation[2]}($entries[$relation[1]][$row[$aliasedIdentifiers[$relation[1]]]], false);
                }
            }
        }

        return $returnAll ? $entries : reset($entries);
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

    protected function _join($alias, $on = null, $type = 'LEFT')
    {
        return ' '.$type.' JOIN '.$this->tables[$alias]->getTableName().' '.$alias.' '.($on ? 'ON '.$on : '').' ';
    }

    protected function _from($alias = null)
    {
        return ' FROM '.$this->tables[$alias]->getTableName().' '.$alias.' ';
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

    protected function _getIdentifiers($alias = null, $condition = null, $values = null, $order = null, $limit = null, $offset = null)
    {
        $sql = 'SELECT '.($alias ? $alias.'.' : '').$this->tables[$alias]->getIdentifier().' FROM '.$this->tables[$alias]->getTableName().' '.$alias.' ';
        if ($condition) $sql .= ' WHERE '.$condition;
        if ($order) $sql .= ' ORDER BY '.$order;
        if ($limit || $offset) $sql .= ' LIMIT '.intval($offset).', '.intval($limit).' ';

        if (!is_array($values) && $values) {
            $values = array($values);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);

        $ids = array();
        foreach($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
            $ids[] = $row[0];
        }
        return $ids;
    }
    

}
