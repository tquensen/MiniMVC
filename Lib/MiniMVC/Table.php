<?php

class MiniMVC_Table {

    /**
     * @var PDO
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
     *
     * @return string the classname of the model
     */
    public function getTableName()
    {
        return $this->_table;
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

		return (isset($this->_entries[$id]) && !$reload) ? $this->_entries[$id] : $this->loadOneBy($this->_identifier, $id);
	}

    /**
     * @param string $field the column to search in
     * @param mixed $value the value of the column
     * @param string $order
     * @param int $offset
     * @return Mysql_Model
     */
	public function loadOneBy($condition, $value = null, $order = null, $offset = 0)
	{
        return $this->query()->where($condition)->orderBy($order)->limit(1, $offset)->build($value);
	}

    /**
     *
     * @param string $order
     * @return array
     */
	public function loadAll($order = null)
	{
		return $this->load(null, null, $order);
	}

    /**
     * @param string $condition the where condition("id = 1", "a.username LIKE 'foo%'")
     * @param string $order an order by clause (id ASC, foo DESC)
     * @param int $limit
     * @param int $offset
     * @return array
     */
	public function load($condition = null, $value = null, $order = null, $limit = null, $offset = null)
	{
        return $this->query()->where($condition)->orderBy($order)->limit($limit, $offset)->build($value);
	}


    /**
     * @param string $condition the where condition("id = 1", "username LIKE 'foo%'")
     * @return int num results found
     */
	public function count($condition = null, $values = null)
	{
        if (!is_array($values) && $values) {
            $values = array($values);
        }
        
        $sql  = 'SELECT COUNT(*) FROM '.$this->_table;
        if ($condition) $sql .= ' WHERE '.$condition;

        $stmt = $this->_db->prepare($sql);

        $stmt->execute($values);


		$result = $stmt->fetch(PDO::FETCH_NUM);
		return $row[0];
	}

    public function query($alias = null)
    {
        $q = new MiniMVC_Query($this->_db);
        return $q->select($alias)->from($this, $alias);
    }
    
	public function buildModel($row, $alias = null)
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
            $values = array();
            $sql = 'INSERT INTO '.$this->_table.' SET ';
			$columnSql = '';
			foreach ($this->_columns as $column)
			{
				if (isset($entry->$column) && $entry->$column !== null)
				{
					$columnSql[] = ' '.$column.' = ? ';
                    $values[] = $entry->$column;
				}
			}
			$sql .= implode(', ', $columnSql);

			$stmt = $this->_db->prepare($sql);
            $result = $stmt->execute($values);

			if ($this->_isAutoIncrement)
			{
				$entry->{$this->primary} = $this->_db->lastInsertId();
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
            $values = array();
			$columnSql = '';
			foreach ($this->_columns as $column)
			{
				if (isset($entry->$column) && $entry->$column !== $entry->getDatabaseProperty($column))
				{
					$columnSql[] = ' '.$column.' = ? ';
                    $values[] = $entry->$column;
                    $update = true;
				}
			}
            if ($update) {
                if ($entry->preUpdate() === false) {
                    return false;
                }
                $sql = 'UPDATE '.$this->_table.' SET ';
                $sql .= implode(', ', $columnSql).' WHERE '.$this->_identifier.' = ?';
                $values[] = $entry->{$this->_identifier};

                $stmt = $this->_db->prepare($sql);
                $result = $stmt->execute($values);
                
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
             WHERE  '.$this->_identifier.' = ?
             LIMIT 1';

			$stmt = $this->_db->prepare($sql);
            $result = $stmt->execute(array($entry->{$this->_identifier}));

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
             WHERE  '.$this->_identifier.' = ?
             LIMIT 1';

			$stmt = $this->_db->prepare($sql);
            $result = $stmt->execute(array($entry));

			if (isset($this->_entries[$entry]))
			{
				unset($this->_entries[$entry]);
			}
		}
		return $result;
	}

	public function deleteBy($condition, $values)
	{
        if (!is_array($values) && $values) {
            $values = array($values);
        }
        
		$sql = 'DELETE
         FROM   '.$this->_table.'
         WHERE  '.$condition;
		$stmt = $this->_db->prepare($sql);
        $result = $stmt->execute($values);

		return $result;
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
}
