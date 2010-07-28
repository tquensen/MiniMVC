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
		$this->db = DB::get();
		$this->registry = MiniMVC_Registry::getInstance();
	}

    /**
     * @param mixed $id the identifier of the entry to get
     * @return Mysql_Model the entry
     */
	public function get($id)
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
		return array_shift($this->getBy($field, $value, $order, 1, $offset));
	}

    /**
     * @param string $field the column to search in
     * @param mixed $value the value of the column
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
	public function getBy($field, $value, $order = null, $limit = null, $offset = null)
	{
		$return = array();
		foreach ($this->entries as $entry)
		{
			if (isset($entry->$field) && $entry->$field == $value)
			{
				$return[$entry->{$this->primary}] = $entry;
			}
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
     * @param array $entries
     * @return bool
     */
	public function setAll($entries)
	{
		if (!is_array($entries))
		{
			return false;
		}
		foreach ($entries as $entry)
		{
			$this->set($entry);
		}
		return true;
	}

    /**
     *
     * @param mixed $id the identifier
     * @return Mysql_Model
     */
	public function load($id)
	{
		$entries = $this->loadOneBy($this->primary, $id);
		return (isset($entries[$id])) ? $entries[$id] : false;
	}

    /**
     * @param string $field the column to search in
     * @param mixed $value the value of the column
     * @param string $order
     * @param int $offset
     * @return Mysql_Model
     */
	public function loadOneBy($field, $value, $order = null, $offset = 0)
	{
		return array_shift($this->loadBy($field, $value, $order, 1, $offset));
	}

    /**
     * @param string $field the column to search in
     * @param mixed $value the value of the column
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
	public function loadBy($field, $value, $order = null, $limit = null, $offset = null)
	{
		if (!$this->primary)
		{
			return array();
		}
		if (!in_array($field, $this->columns))
		{
			return array();
		}

        $sql  = $this->_select('a');
        $sql .= $this->_from('a');
        $sql .= $this->_where('a.'.$field, $this->serialize($value));   
		$sql .= $this->_limit($limit, $offset);
		$sql .= $this->_order($order);

		$result = $this->db->query($sql);

		$entries = $this->_buildEntries($result, 'a');
		$this->set($entries);
		return $entries;
	}

    /**
     *
     * @param string $order
     * @return <type>
     */
	public function loadAll($order = null)
	{
		if (!$this->primary)
		{
			return array();
		}

		$entries = array();

        $sql  = $this->_select('a');
        $sql .= $this->_from('a');
        $sql .= $this->_order($order);
		
		$result = $this->db->query($sql);

		$entries = $this->_buildEntries($result, 'a');
		$this->set($entries);
		return $entries;
	}

    public function _select($alias, $first = false)
    {
        $sql = $first ? 'SELECT ' : ', ';
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

    protected function _from($alias)
    {
        return ' FROM '.$this->table.' '.$alias.' ';
    }

    public function _join($alias, $foreignAlias, $identifier, $foreignIdentifier, $type = 'LEFT')
    {
        return ' '.$type.' JOIN '.$this->table.' '.$alias.' ON '.$foreignAlias.'.'.$foreignIdentifier. ' = '.$alias.'.'.$identifier.' ';
    }

    protected function _where($field, $value, $operator = 'WHERE', $condition = '=')
    {
        return ' '.$operator.' '.$field.' '.$condition.' "'.$this->db->real_escape_string($value).'" ';
    }

	protected function _order($order = null)
	{
		if ($order !== null && $order !== false)
		{
			$order = explode(' ', $order, 2);
			if (!isset($order[1]) || !in_array(strtolower(trim($order[1])), array('asc', 'desc')))
			{
				return false;
			}
			return ' ORDER BY '.$order[0].' '.strtolower(trim($order[1])).' ';
		}
		return '';
	}

	protected function _limit($limit = null, $offset = null)
	{
		if ($limit !== null || $offset !== null)
		{
			return ' LIMIT '.intval($offset).', '.intval($limit).' ';
		}
		return '';
	}

	protected function _buildEntries($result, $alias)
	{
		if (!$result || !$result->num_rows)
		{
			return array();
		}
		$return = array();
		while ($row = $result->fetch_assoc())
		{
            if ($alias) {
                $row = $this->_filter($row, $alias);
            }
			$entry = new $this->entryClass($this);
            $entry->isNew(false);
			foreach ($row as $k=>$v)
			{
				$entry->$k = $this->unserialize($v);
			}
			$this->buildEntry($entry);
			$this->set($entry);
			$return[$entry->{$this->primary}] = $entry;
		}
	}

    protected function _filter($row, $alias = '')
    {
        if (!$alias) {
            return $row;
        }
        $prefix = $alias.'__';
        $length = strlen($prefix);
        foreach ($row as $k=>$v) {
            if (substr($v, 0, $length) == $prefix) {
               $row[$k] = substr($v, $lenght);
            } else {
                unset($row[$k]);
            }
        }
        return $row;
    }

	protected function buildEntry($entry)
	{
		/*
		 $entry->additionalData = $entry->id.'Something';
		 */
		return $entry;
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

        $this->buildEntry($entry);
		$this->set($entry);
		return $entry;
	}

	public function save($entry)
	{
        if ($entry->isNew()) {
            $sql = 'INSERT INTO '.$this->table.' SET ';
			$columnSql = '';
			foreach ($this->columns as $column)
			{
				if (isset($entry->$column) && $entry->$column !== null)
				{
					$columnSql[] = ' '.$column.' = "'.$this->db->real_escape_string(($this->serialize($entry->$column))).'" ';
				}
			}
			$sql .= implode(', ', $columnSql).';';
			$result = $this->db->query($sql);

			if ($this->isAutoIncrement)
			{
				$entry->{$this->primary} = $this->db->insert_id;
			}
        } else {
            $sql = 'UPDATE '.$this->table.' SET ';
			$columnSql = '';
			foreach ($this->columns as $column)
			{
				if (isset($entry->$column) && $entry->$column !== null)
				{
					$columnSql[] = ' '.$column.' = "'.$this->db->real_escape_string(($this->serialize($entry->$column))).'" ';
				}
			}
			$sql .= implode(', ', $columnSql).' WHERE '.$this->primary.' = "'.$this->db->real_escape_string($this->serialize($entry->{$this->primary})).'" ;';
			$result = $this->db->query($sql);
        }

		$this->set($entry);
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

    /**
     *
     * @return MiniMVC_Table
     */
    public static function get()
    {
        return new self;
    }
    
}
