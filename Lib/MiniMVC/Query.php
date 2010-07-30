<?php

class MiniMVC_Query
{
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
    public function join($table, $alias, $related, $on, $type = 'LEFT', $reverse = false)
    {
        $this->tables[$alias] = $table;
        $this->join[$alias] = array($on, $type);
        $this->relations[] = array($related, $alias);
        if ($reverse) {
            $this->relations[] = array($alias, $related);
        }

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
    public function get()
    {
        $q = 'SELECT ';
        $comma = false;
        $select = array();
        foreach ($this->select as $k => $v) {
            if (isset($this->tables[$v])) {
                $select[] = $this->tables[$v]->_select($v, false);
            } else {
                $select[] = $v;
            }
        }
        $q .= implode(', ', $select);
        if (isset($this->tables[$this->from])) {
            $q .= $this->tables[$this->from]->_from($this->from);
        }
        foreach ($this->join as $join => $info) {
            if (isset($this->tables[$join])) {
                $q .= $this->tables[$join]->_join($join, $info[0], $info[1]);
            }
        }
        $condition = count($this->where) ? implode(' AND ', $this->where) : '';
        if ($this->where) {
            $q .= ' WHERE '.$condition.' ';
        }
        if ($this->limit || $this->offset) {
            if ($this->needPreQuery) {
                $limit = '';
                $q .= ($condition ? ' AND ' : ' WHERE ') . $this->tables[$this->from]->_in($this->from, null, $this->tables[$this->from]->_getIdentifiers($this->from, $condition, $this->order, $this->limit, $this->offset));
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

    public function build($returnAll = false)
    {
        $sql = $this->get();
        
        if (count($this->tables) === 1) {
            $aliases = $this->from;
            $relations = null;
        } else {
            $aliases = array();
            $relations = array();
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
        }

        return $this->tables[$this->from]->buildAll($sql, $aliases, $relations, $returnAll);
    }
    

}
