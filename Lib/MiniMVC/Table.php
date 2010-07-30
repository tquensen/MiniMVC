<?php

class MiniMVC_Table {

    /**
     * @var mysqli
     */
	protected $_db = null;
    /**
     * @var MiniMVC_Registry 
     */
    protected $_registry = null;

	protected $_entries = array();
	protected $_table = false;
    protected $_model = 'MiniMVC_Model';

	protected $_columns = array('id');
    protected $_relations = array();
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;


	public function __construct()
	{
		$this->_registry = MiniMVC_Registry::getInstance();
		$this->_db = $this->_registry->db->get();
        $this->construct();
	}

    protected function construct()
    {

    }

    /**
     *
     * @return mixed the column name of the primary key
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function getRelations()
    {
        return $this->_relations;
    }

    public function getRelation($relation)
    {
        return isset($this->_relations[$relation]) ? $this->_relations[$relation] : null;
    }

    /**
     *
     * @return string the classname of the model
     */
    public function getModelName()
    {
        return $this->_model;
    }

    /**
     * @param mixed $id the identifier of the entry to get
     * @return Mysql_Model the entry
     */
	public function getOne($id)
	{
		return (isset($this->_entries[$id])) ? $this->_entries[$id] : null;
	}

    /**
     * @param string $field the column to search in
     * @param mixed $value the value of the column
     * @param string|bool $order
     * @param int $offset
     * @return Mysql_Model
     */
	public function getOneBy($field, $value, $order = null, $offset = 0)
	{
		return array_shift($this->get($field, $value, $order, 1, $offset));
	}

    /**
     * @param string $field the column to search in
     * @param mixed $value the value of the column
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
	public function get($field = null, $value = null, $order = null, $limit = null, $offset = null)
	{
		$return = array();
        if ($field) {
            foreach ($this->_entries as $entry)
            {
                if (isset($entry->$field) && $entry->$field == $value)
                {
                    $return[$entry->{$this->_identifier}] = $entry;
                }
            }
        } else {
            $return = $this->_entries;
        }
		if ($order !== null && $order !== false)
		{
			$order = explode(' ', $order, 2);
			if (!isset($order[1]) || !in_array(strtolower(trim($order[1])), array('asc', 'desc')) || !in_array(trim($order[0], $this->_columns)))
			{
				return array();
			}
			$return = $this->orderBy($order[0], $order[1], $return);
		}
		if ($limit !== null || $offset !== null)
		{
			$return = array_splice($return, intval($offset), intval($limit));
		}

		return $return;
	}

    /**
     *
     * @param bool $reload force database query
     * @return array
     */
	public function getAll($reload = false)
	{
		if ($reload || !$this->_entries)
		{
			$this->loadAll();
		}
		return $this->_entries;
	}

    /**
     *
     * @param Mysql_Model $entry
     * @return bool
     */
	public function set($entry)
	{
        if (is_array($entry)) {
            foreach ($entry as $currentEntry) {
                $this->set($currentEntry);
            }
        }
		if (!isset($entry->{$this->_identifier}))
		{
			return false;
		}
		$this->_entries[$entry->{$this->_identifier}] = $entry;
		return true;
	}

    /**
     *
     * @param mixed $id the identifier
     * @return Mysql_Model
     */
	public function loadOne($id, $reload = false)
	{

		return (isset($this->_entries[$id]) && !$reload) ? $this->_entries[$id] : $this->loadOneBy($this->_identifier . ' = "'. $id . '"');
	}

    /**
     * @param string $field the column to search in
     * @param mixed $value the value of the column
     * @param string $order
     * @param int $offset
     * @return Mysql_Model
     */
	public function loadOneBy($condition = null, $order = null, $offset = 0)
	{
        return $this->query()->where($condition)->orderBy($order)->limit(1, $offset)->build();
	}

    /**
     *
     * @param string $order
     * @return array
     */
	public function loadAll($order = null)
	{
		return $this->load(null, $order);
	}

    /**
     * @param string $condition the where condition("id = 1", "a.username LIKE 'foo%'")
     * @param string $order an order by clause (id ASC, foo DESC)
     * @param int $limit
     * @param int $offset
     * @return array
     */
	public function load($condition = null, $order = null, $limit = null, $offset = null)
	{
        return $this->query()->where($condition)->orderBy($order)->limit($limit, $offset)->build();
	}


    /**
     * @param string $condition the where condition("id = 1", "username LIKE 'foo%'")
     * @return int num results found
     */
	public function count($condition = null, $value = null)
	{
        $sql  = 'SELECT COUNT(*) num'.$this->_from();
        if ($condition) $sql .= ' WHERE '.$condition;
		$result = $this->_db->query($sql);
        $row = $result->fetch_assoc();
		return $row['num'];
	}

    public function query($alias = null)
    {
        $q = new MiniMVC_Query();
        return $q->select($alias)->from($this, $alias);
    }

    public function _getIdentifiers($alias = null, $condition = null, $order = null, $limit = null, $offset = null)
    {
        $sql = 'SELECT '.($alias ? $alias.'.' : '').$this->_identifier.' FROM '.$this->_table.' '.$alias.' ';
        if ($condition) $sql .= ' WHERE '.$condition;
        if ($order) $sql .= ' ORDER BY '.$order;
        if ($limit || $offset) $sql .= ' LIMIT '.intval($offset).', '.intval($limit).' ';
        $ids = array();

        $result = $this->_db->query($sql);

        $ids = array();
        while($row = $result->fetch_assoc()) {
            $ids[] = $row['id'];
        }
        return $ids;
    }

    public function _select($alias = null, $prefix = null)
    {
        $sql = $prefix === true ? ' , ' : $prefix;

        if (!$alias) {
            $sql .= implode(', ', $this->_columns).' ';
            return $sql;
        }
        $comma = false;
        foreach ($this->_columns as $column) {
            if ($comma) {
                $sql .= ', ';
            } else {
                $comma = true;
            }
            $sql .= $alias.'.'.$column.' '.$alias.'__'.$column.' ';
        }
        return $sql;
    }

    public function _join($alias, $on = null, $type = 'LEFT')
    {
        return ' '.$type.' JOIN '.$this->_table.' '.$alias.' '.($on ? 'ON '.$on : '').' ';
    }

    public function _from($alias = null)
    {
        return ' FROM '.$this->_table.' '.$alias.' ';
    }

    public function _in($alias = null, $key = null, $values = array())
    {
        if (empty($values) || !is_array($values)) {
            return  ' ';
        }
        if (!$key) {
            $key = $this->_identifier;
        }
        if ($alias) {
            $key = $alias.'.'.$key;
        }
        return ' '.$key.' IN ("'.implode('","',  array_map(array($this->_db, 'real_escape_string'), $values)).'") ';
    }
    
    public function buildAll($result, $aliases = null, $relations = array(), $returnAll = false)
    {
        if (is_string($result)) {
            $result = $this->_db->query($result);
        }
        $single = false;
        $entries = array();
        $aliasedIdentifiers = array();
        $entryClasses = array();
        if ($aliases === null) {
            while ($row = $result->fetch_assoc()) {
                $entries[] = $this->_buildModel($row);
            }
            return $entries;
        } elseif (is_string($aliases)) {
            $identifier = $aliases.'__'.$this->_identifier;
            while ($row = $result->fetch_assoc()) {
                if ($row[$identifier] && !isset($entries[$row[$identifier]])) {
                    $entries[$row[$identifier]] = $this->_buildModel($row, $aliases);
                }
            }
            return $entries;
        }
        foreach ($aliases as $alias => $buildClass) {
                $aliasedIdentifiers[$alias] = $alias.'__'.$buildClass->getIdentifier();
                $entryClasses[$alias] = $buildClass->getModelName();
        }
        if (!empty($relations) && !is_array($relations[0])) {
            $relations = array($relations);
        }
        while ($row = $result->fetch_assoc()) {
            foreach ($aliases as $alias => $buildClass) {
                if ($row[$aliasedIdentifiers[$alias]] && !isset($entries[$alias][$row[$aliasedIdentifiers[$alias]]])) {
                    $entries[$alias][$row[$aliasedIdentifiers[$alias]]] = $buildClass->_buildModel($row, $alias);
                }
            }
            foreach ($relations as $relation) {
                if ($row[$aliasedIdentifiers[$relation[0]]] && $row[$aliasedIdentifiers[$relation[1]]]) {
                    $entries[$relation[0]][$row[$aliasedIdentifiers[$relation[0]]]]->{'set'.(isset($relation[2]) ? $relation[2] : $entryClasses[$relation[1]])}($entries[$relation[1]][$row[$aliasedIdentifiers[$relation[1]]]], false);
                }
            }
        }
        return $returnAll ? $entries : reset($entries);
    }

	protected function _buildModel($row, $alias = null)
	{
        if ($alias) {
            $row = $this->_filter($row, $alias);
        }
        if (empty($row) || !isset($row[$this->_identifier])) {
            return null;
        }
        $entry = new $this->_model($this);
        foreach ($row as $k=>$v)
        {
            $v = $this->unserialize($v);
            $entry->$k = $v;
            if (in_array($k, $this->_columns)) {
                $entry->setDatabaseProperty($k, $v);
            }
        }
        $entry->postLoad();
        $this->set($entry);
        return $entry;
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


	public function create($data = array())
	{
        $entry = new $this->_model($this);

        $default = array();
        foreach ($this->_columns as $column)
        {
            $default[$column] = null;
        }
        $data = array_merge($default, $data);
        foreach ($data as $k=>$v)
        {
            $entry->$k = $v;
        }

        $entry->postCreate();
		$this->set($entry);
		return $entry;
	}

	public function save($entry)
	{
        if ($entry->preSave() === false) {
            return false;
        }
        if ($entry->isNew()) {
            if ($entry->preInsert() === false) {
                return false;
            }
            $sql = 'INSERT INTO '.$this->_table.' SET ';
			$columnSql = '';
			foreach ($this->_columns as $column)
			{
				if (isset($entry->$column) && $entry->$column !== null)
				{
					$columnSql[] = ' '.$column.' = "'.$this->_db->real_escape_string(($this->serialize($entry->$column))).'" ';
                    $entry->setDatabaseProperty($column, $entry->$column);
				}
			}
			$sql .= implode(', ', $columnSql).';';
			$result = $this->_db->query($sql);

			if ($this->_isAutoIncrement)
			{
				$entry->{$this->primary} = $this->_db->insert_id;
			}

            foreach ($this->_columns as $column)
			{
                $entry->setDatabaseProperty($column, $entry->$column);
            }

            if ($entry->postInsert() === false) {
                return false;
            }

        } else {
            $update = false;
			$columnSql = '';
			foreach ($this->_columns as $column)
			{
				if (isset($entry->$column) && $entry->$column !== $entry->getDatabaseProperty($column))
				{
					$columnSql[] = ' '.$column.' = "'.$this->_db->real_escape_string(($this->serialize($entry->$column))).'" ';
                    $update = true;
				}
			}
            if ($update) {
                if ($entry->preUpdate() === false) {
                    return false;
                }
                $sql = 'UPDATE '.$this->_table.' SET ';
                $sql .= implode(', ', $columnSql).' WHERE '.$this->_identifier.' = "'.$this->_db->real_escape_string($this->serialize($entry->{$this->_identifier})).'" ;';
                $result = $this->_db->query($sql);
                foreach ($this->_columns as $column)
                {
                    if (isset($entry->$column) && $entry->$column !== $entry->getDatabaseProperty($column))
                    {
                        $entry->setDatabaseProperty($column, $entry->$column);
                    }
                }
                if ($entry->postUpdate() === false) {
                    return false;
                }
            }
        }

		$this->set($entry);
        if ($entry->postSave() === false) {
            return false;
        }
		return (bool) $result;
	}

	public function delete($entry)
	{
		if (is_object($entry))
		{
			if (!isset($entry->{$this->_identifier}) || !$entry->{$this->_identifier})
			{
				return false;
			}

            if ($entry->preDelete() === false) {
                return false;
            }

			$sql = 'DELETE
             FROM   '.$this->_table.'
             WHERE  '.$this->_identifier.' = "'.$this->_db->real_escape_string($entry->{$this->_identifier}).'"
             LIMIT 1
             ;';

			$result = $this->_db->query($sql);
			if (isset($this->_entries[$entry->{$this->primary}]))
			{
				unset($this->_entries[$entry->{$this->primary}]);
			}
            $entry->clearDatabaseProperties();

            if ($entry->postDelete() === false) {
                return false;
            }
		}
		else
		{
			$sql = 'DELETE
             FROM   '.$this->_table.'
             WHERE  '.$this->_identifier.' = "'.$this->_db->real_escape_string($entry).'"
             LIMIT 1
             ;';

			$result = $this->_db->query($sql);
			if (isset($this->_entries[$entry]))
			{
				unset($this->_entries[$entry]);
			}
		}
		return $this->_db->affected_rows();
	}

	public function deleteBy($field, $value)
	{
		//validate field!
		if (!in_array($field, $this->_columns))
		{
			return false;
		}
		$sql = 'DELETE
         FROM   '.$this->_table.'
         WHERE  '.$field.' = "'.$this->_db->real_escape_string($this->serialize($value)).'"
         ;';
		$result = $this->_db->query($sql);
		$return = $this->_db->affected_rows();
		foreach ($this->_entries as $id => $entry)
		{
			if (isset($entry->$field) && $entry->$field == $value)
			{
				unset($this->_entries[$id]);
			}
		}

		return $return;
	}

	public function orderBy($field, $direction, $entries)
	{
		$dir = (strtolower($direction) == 'asc') ? SORT_ASC : SORT_DESC;
		$fields = array();
		foreach ($entries as $key => $row) {
			if (!isset($row->$field))
			{
				return array();
			}
			$fields[$key]    = $row->$field;
		}


		array_multisort($fields, $dir, $entries);

		$newEntries = array();
		foreach ($entries as $entry)
		{
			$newEntries[$entry->{$this->_identifier}] = $entry;
		}
		return $newEntries;
	}

    public function install()
    {

    }

    public function uninstall()
    {

    }

    protected function serialize($data)
    {
        return $data;
    }

    protected function unserialize($data)
    {
        return $data;
    }
}
