<?php

class MiniMVC_Table {

    /**
     * @var mysqli
     */
	protected $db = null;
    /**
     * @var MiniMVC_Registry 
     */
    protected $registry = null;

	protected $entries = array();
	protected $table = false;
    protected $entryClass = 'Mysql_Model';

	protected $columns = array('id');
	protected $primary = 'id';
	protected $isAutoIncrement = true;


	public function __construct()
	{
		$this->registry = MiniMVC_Registry::getInstance();
		$this->db = $this->registry->db->get();
	}

    /**
     * @param mixed $id the identifier of the entry to get
     * @return Mysql_Model the entry
     */
	public function getOne($id)
	{
		return (isset($this->entries[$id])) ? $this->entries[$id] : false;
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
            foreach ($this->entries as $entry)
            {
                if (isset($entry->$field) && $entry->$field == $value)
                {
                    $return[$entry->{$this->primary}] = $entry;
                }
            }
        } else {
            $return = $this->entries;
        }
		if ($order !== null && $order !== false)
		{
			$order = explode(' ', $order, 2);
			if (!isset($order[1]) || !in_array(strtolower(trim($order[1])), array('asc', 'desc')) || !in_array(trim($order[0], $this->columns)))
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
		if ($reload || !$this->entries)
		{
			$this->loadAll();
		}
		return $this->entries;
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
		if (!isset($entry->{$this->primary}))
		{
			return false;
		}
		$this->entries[$entry->{$this->primary}] = $entry;
		return true;
	}

    /**
     *
     * @param mixed $id the identifier
     * @return Mysql_Model
     */
	public function loadOne($id)
	{
		return $this->loadOneBy($this->primary . ' = "'. $id . '"');
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
		return array_shift($this->load($condition, $order, 1, $offset));
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
        $sql  = $this->_select();
        $sql .= ' FROM '.$this->table;
        if ($condition) $sql .= ' WHERE '.$condition;
        if ($order) $sql .= ' ORDER BY '.$order;
        if ($limit || $offset) $sql .= ' LIMIT '.intval($offset).', '.intval($limit).' ';

		$result = $this->db->query($sql);

        $entries = array();
        while($row = $result->fetch_assoc()) {
            if (!isset($entries[$row[$this->primary]])) {
                $entries[$row[$this->primary]] = $this->_buildEntry($row);
            }
        }
		return $entries;
	}

    /**
     * @param string $condition the where condition("id = 1", "a.username LIKE 'foo%'")
     * @return int num results found
     */
	public function count($condition = null, $value = null)
	{
        $sql  = 'SELECT COUNT(*) num';
        $sql .= ' FROM '.$this->table;
        if ($condition) $sql .= ' WHERE '.$condition;
		$result = $this->db->query($sql);
        $row = $result->fetch_assoc();
		return $row['num'];
	}

    /**
     *
     * @param string $order
     * @return <type>
     */
	public function loadAll($order = null)
	{
		return $this->load(null, null, $order);
	}

    

    public function _select($alias = null, $first = true)
    {
        $sql = $first ? ' SELECT ' : ', ';
        if (!$alias) {
            $sql .= implode(', ', $this->columns).' ';
            return $sql;
        }
        $comma = false;
        foreach ($this->columns as $column) {
            if ($comma) {
                $sql .= ', ';
            } else {
                $comma = true;
            }
            $sql .= $alias.'.'.$column.' '.$alias.'__'.$column.' ';
        }
        return $sql;
    }
    

	public function _buildEntry($row, $alias)
	{
        if ($alias) {
            $row = $this->_filter($row, $alias);
        }
        if (empty($row) || !isset($row[$this->primary])) {
            return null;
        }
        $entry = new $this->entryClass($this);
        foreach ($row as $k=>$v)
        {
            $v = $this->unserialize($v);
            $entry->$k = $v;
            if (in_array($k, $this->columns)) {
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
        $entry = new $this->entryClass($this);

        $default = array();
        foreach ($this->columns as $column)
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
            $sql = 'INSERT INTO '.$this->table.' SET ';
			$columnSql = '';
			foreach ($this->columns as $column)
			{
				if (isset($entry->$column) && $entry->$column !== null)
				{
					$columnSql[] = ' '.$column.' = "'.$this->db->real_escape_string(($this->serialize($entry->$column))).'" ';
                    $entry->setDatabaseProperty($column, $entry->$column);
				}
			}
			$sql .= implode(', ', $columnSql).';';
			$result = $this->db->query($sql);

			if ($this->isAutoIncrement)
			{
				$entry->{$this->primary} = $this->db->insert_id;
			}

            foreach ($this->columns as $column)
			{
                $entry->setDatabaseProperty($column, $entry->$column);
            }

            if ($entry->postInsert() === false) {
                return false;
            }

        } else {
            $update = false;
			$columnSql = '';
			foreach ($this->columns as $column)
			{
				if (isset($entry->$column) && $entry->$column !== $entry->getDatabaseProperty($column))
				{
					$columnSql[] = ' '.$column.' = "'.$this->db->real_escape_string(($this->serialize($entry->$column))).'" ';
                    $entry->setDatabaseProperty($column, $entry->$column);
                    $update = true;
				}
			}
            if ($update) {
                if ($entry->preUpdate() === false) {
                    return false;
                }
                $sql = 'UPDATE '.$this->table.' SET ';
                $sql .= implode(', ', $columnSql).' WHERE '.$this->primary.' = "'.$this->db->real_escape_string($this->serialize($entry->{$this->primary})).'" ;';
                $result = $this->db->query($sql);
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
			if (!isset($entry->{$this->primary}) || !$entry->{$this->primary})
			{
				return false;
			}

            if ($entry->preDelete() === false) {
                return false;
            }

			$sql = 'DELETE
             FROM   '.$this->table.'
             WHERE  '.$this->primary.' = "'.$this->db->real_escape_string($entry->{$this->primary}).'"
             LIMIT 1
             ;';

			$result = $this->db->query($sql);
			if (isset($this->entries[$entry->{$this->primary}]))
			{
				unset($this->entries[$entry->{$this->primary}]);
			}
            $entry->clearDatabaseProperties();

            if ($entry->postDelete() === false) {
                return false;
            }
		}
		else
		{
			$sql = 'DELETE
             FROM   '.$this->table.'
             WHERE  '.$this->primary.' = "'.$this->db->real_escape_string($entry).'"
             LIMIT 1
             ;';

			$result = $this->db->query($sql);
			if (isset($this->entries[$entry]))
			{
				unset($this->entries[$entry]);
			}
		}
		return $this->db->affected_rows();
	}

	public function deleteBy($field, $value)
	{
		//validate field!
		if (!in_array($field, $this->columns))
		{
			return false;
		}
		$sql = 'DELETE
         FROM   '.$this->table.'
         WHERE  '.$field.' = "'.$this->db->real_escape_string($this->serialize($value)).'"
         ;';
		$result = $this->db->query($sql);
		$return = $this->db->affected_rows();
		foreach ($this->entries as $id => $entry)
		{
			if (isset($entry->$field) && $entry->$field == $value)
			{
				unset($this->entries[$id]);
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
			$newEntries[$entry->{$this->primary}] = $entry;
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

    /**
     * @param MiniMVC_Model $record The record to use for default values and to save the results in
     * @param array $options form options
     * @return MiniMVC_Form a form class instance
     */
    public function getForm($record = null, $options = array())
    {
        if (!$record) {
            $record = new $this->entryClass($this);
        }
        return new MiniMVC_Form($record, $options);
    }
    
}
