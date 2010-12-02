<?php

class MiniMVC_Table {

    /**
     * @var PDO
     */
	protected $_db = null;
    /**
     * @var MiniMVC_Registry 
     */
    protected $registry = null;

	protected $_entries = array();
	protected $_table = false;
    protected $_model = 'MiniMVC_Model';

	protected $_columns = array('id');
    protected $_relations = array();
	protected $_identifier = 'id';
	protected $_isAutoIncrement = true;

    protected $_returnTypes = array('object' => 'build', 'array' => 'buildArray', 'query' => 'getQueryObject');

	public function __construct()
	{
		$this->registry = MiniMVC_Registry::getInstance();
		$this->_db = $this->registry->db->get();
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

    /**
     *
     * @return PDO the database
     */
    public function getDb()
    {
        return $this->_db;
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
     *
     * @param mixed $id the identifier
     * @return Mysql_Model
     */
	public function loadOne($id)
	{
		return $this->loadOneBy($this->_identifier . ' = ?', $id);
	}

    /**
     * @param string $condition the search condition (? = placeholder)
     * @param mixed $value the value(s) for the placeholders in the condition
     * @param string $order
     * @param int $offset
     * @return Mysql_Model
     */
	public function loadOneBy($condition, $value = null, $order = null, $offset = 0)
	{
        $result = $this->query()->where($condition)->orderBy($order)->limit(1, $offset)->build($value);
        return is_array($result) ? reset($result) : null;
	}

    /**
     * loads entries with related entries
     *
     * an relationinfo array looks like:
     * array('alias.relation', 'foreignalias', true) //set third parameter to true to select/fetch the related fields
     * the alias for the current model is 'a'.
     *
     * examples:
     * 1. array('a.Comments', 'c', true); //in this case, if you use limit or offset, set $usePreQuery to true
     * 2. array(array('a.Metadata', 'm', true)array('a.Comments', 'c', true), array('c.User', 'cu', true));
     *
     * @param mixed $id the identifier
     * @param array $relations either one array or a an array containing arrays with relationinfos
     * @param string $condition the where condition("id = ?", "a.username LIKE ?")
     * @param mixed $values the values for the ?-placeholders
     * @param string $order an order by clause (id ASC, foo DESC)
     * @param int $offset
     * @param bool $needPreQuery set this to true if you use limit or offset with a 1-to-many left join (to limit the resulting entries, not the table rows)
     * @return Mysql_Model
     */
	public function loadOneWithRelations($id, $relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = false)
	{
        $value = (array) $value;
        array_unshift($value, $id);
        $results = $this->loadWithRelations($relations, $condition ? 'a.'.$this->_identifier . ' = ? AND '.$condition : 'a.'.$this->_identifier . ' = ?', $value, $order, null, $offset, $needPreQuery);
        return is_array($results) ? reset($results) : null;
	}

    /**
     * loads entries with related entries
     *
     * an relationinfo array looks like:
     * array('alias.relation', 'foreignalias', true) //set third parameter to true to select/fetch the related fields
     * the alias for the current model is 'a'.
     *
     * examples:
     * 1. array('a.Comments', 'c', true); //in this case, if you use limit or offset, set $usePreQuery to true
     * 2. array(array('a.Metadata', 'm', true)array('a.Comments', 'c', true), array('c.User', 'cu', true));
     *
     * @param array $relations either one array or a an array containing arrays with relationinfos
     * @param string $condition the where condition("id = ?", "a.username LIKE ?")
     * @param mixed $values the values for the ?-placeholders
     * @param string $order an order by clause (id ASC, foo DESC)
     * @param int $offset
     * @param bool $needPreQuery set this to true if you use limit or offset with a 1-to-many left join (to limit the resulting entries, not the table rows)
     * @return Mysql_Model
     */
	public function loadOneWithRelationsBy($relations = array(), $condition = null, $value = null, $order = null, $offset = 0, $needPreQuery = true)
	{
        $results = $this->loadWithRelations($relations, $condition, $value, $order, null, $offset, $needPreQuery);
        return is_array($results) ? reset($results) : null;
	}

    /**
     * @param string $condition the where condition("id = ?", "username LIKE ?")
     * @param mixed $values the values for the ?-placeholders
     * @return bool true if results were found
     */
	public function exist($condition, $values = null)
	{
        return (bool) $this->count($condition, $values);
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
     * @param string $condition the where condition("id = ?", "a.username LIKE ?")
     * @param mixed $values the values for the ?-placeholders
     * @param string $order an order by clause (id ASC, foo DESC)
     * @param int $limit
     * @param int $offset
     * @param string $returnAs the type of data to return (object, array or query)
     * @return array
     */
	public function load($condition = null, $value = null, $order = null, $limit = null, $offset = null, $returnAs = 'object')
	{
        if (!isset($this->_returnTypes[$returnAs])) {
            $returnAs = 'object';
        }
        return $this->query()->where($condition)->orderBy($order)->limit($limit, $offset)->{$this->_returnTypes[$returnAs]}($value);
	}

    /**
     * loads entries with related entries
     * 
     * an relationinfo array looks like:
     * array('alias.relation', 'foreignalias', true) //set third parameter to true to select/fetch the related fields
     * the alias for the current model is 'a'.
     * 
     * examples:
     * 1. array('a.Comments', 'c', true); //in this case, if you use limit or offset, set $usePreQuery to true
     * 2. array(array('a.Metadata', 'm', true)array('a.Comments', 'c', true), array('c.User', 'cu', true));
     *
     * @param array $relations either one array or a an array containing arrays with relationinfos
     * @param string $condition the where condition("id = ?", "a.username LIKE ?")
     * @param mixed $values the values for the ?-placeholders
     * @param string $order an order by clause (id ASC, foo DESC)
     * @param int $limit
     * @param int $offset
     * @param bool $needPreQuery set this to true if you use limit or offset with a 1-to-many left join (to limit the resulting entries, not the table rows)
     * @param string $returnAs the type of data to return (object, array or query)
     * @return array
     */
	public function loadWithRelations($relations = array(), $condition = null, $value = null, $order = null, $limit = null, $offset = null, $needPreQuery = false, $returnAs = 'object')
	{
        if (!isset($this->_returnTypes[$returnAs])) {
            $returnAs = 'object';
        }
        
        $q = $this->query('a');
        foreach ($relations as $relation) {
            if (!is_array($relation)) {
                if (!empty($relations[0]) && !empty($relations[1])) {
                    if ((!empty($relations[2]) && $relations[2] === true) || (!empty($relations[3]) && $relations[3] === true)) {
                        $q->select($relations[1]);
                    } elseif (!empty($relations['select'])) {
                        $q->select($relations['select']);
                    }
                    $q->join($relations[0], $relations[1], !empty($relations[2]) && $relations[2] !== true ? $relations[2] : null, !empty($relations[3]) && $relations[3] !== true ? $relations[3] : 'LEFT');
                }
                break;
            }
               
            if (!empty($relation[0]) && !empty($relation[1])) {
                if ((!empty($relation[2]) && $relation[2] === true) || (!empty($relation[3]) && $relation[3] === true)) {
                    $q->select($relation[1]);
                } elseif (!empty($relation['select'])) {
                    $q->select($relation['select']);
                }
                $q->join($relation[0], $relation[1], !empty($relation[2]) && $relation[2] !== true ? $relation[2] : null, !empty($relation[3]) && $relation[3] !== true ? $relation[3] : 'LEFT');
            }

        }

        return $q->where($condition)->orderBy($order)->limit($limit, $offset, $needPreQuery)->{$this->_returnTypes[$returnAs]}($value);
	}


    /**
     * @param string $condition the where condition("id = ?", "username LIKE ?")
     * @param mixed $values the values for the ?-placeholders
     * @return int num results found
     */
	public function count($condition = null, $values = null)
	{
        if (!is_array($values) && $values !== null) {
            $values = array($values);
        }
        if ($stmt = $this->query(null, 'COUNT(*)')->where($condition)->execute($values)) {
            return $stmt->fetchColumn();
        }
        return false;
	}

    /**
     *
     * @param string $alias the alias of this model
     * @param bool $select true to add a $query->select($alias)
     * @return MiniMVC_Query
     */
    public function query($alias = null, $select = true)
    {
        $q = $this->registry->db->query();
        if ($select === true) {
            $q->select($alias);
        } elseif($select) {
            $q->select($select);
        }
        return $q->from($this, $alias);
    }
    
	public function buildModel($row)
	{
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
        return $entry;
	}

	public function create($data = array())
	{
        $entry = new $this->_model($this);

        foreach ($data as $k=>$v)
        {
            $entry->$k = $v;
        }

        $entry->postCreate();
		return $entry;
	}

	public function save($entry)
	{
        try
        {
            $this->_db->beginTransaction();

            if ($entry->preSave() === false) {
                $this->_db->rollBack();
                return false;
            }

            if ($entry->isNew()) {
                if ($entry->preInsert() === false) {
                    $this->_db->rollBack();
                    return false;
                }


                $fields = array();
                $values = array();
                foreach ($this->_columns as $column)
                {
                    if (isset($entry->$column) && $entry->$column !== null)
                    {
                        $fields[] = $column;
                        //$query->set(' '.$column.' = ? ');
                        $values[] = $entry->$column;
                    }
                }

                $query = $this->query()->insert($fields, $values);


                $result = $query->execute();

                if ($this->_isAutoIncrement)
                {
                    $entry->{$this->_identifier} = $this->_db->lastInsertId();
                }

                foreach ($this->_columns as $column)
                {
                    $entry->setDatabaseProperty($column, $entry->$column);
                } 

                if ($entry->postInsert() === false) {
                    $this->_db->rollBack();
                    return false;
                }

            } else {
                $update = false;

                if ($entry->preUpdate() === false) {
                    $this->_db->rollBack();
                    return false;
                }
                
                $fields = array();
                $values = array();
                foreach ($this->_columns as $column)
                {
                    if ($entry->$column !== $entry->getDatabaseProperty($column))
                    {
                        $fields[] = $column;
                        //$query->set(' '.$column.' = ? ');
                        $values[] = $entry->$column;
                        $update = true;
                    }
                }

                $query = $this->query()->update($fields)->where($this->_identifier.' = ?');

                if (!$update) {
                    $this->_db->rollBack();
                    return true;
                }

                $values[] = $entry->{$this->_identifier};

                $result = $query->execute($values);

                foreach ($this->_columns as $column)
                {
                    if (isset($entry->$column) && $entry->$column !== $entry->getDatabaseProperty($column))
                    {
                        $entry->setDatabaseProperty($column, $entry->$column);
                    }
                }

                if ($entry->postUpdate() === false) {
                    $this->_db->rollBack();
                    return false;
                }

            }

            if ($entry->postSave() === false) {
                $this->_db->rollBack();
                return false;
            }
            
            $this->_db->commit();
        } catch (PDOException $e) {
            $this->_db->rollBack();
            return false;
        }

		return (bool) $result;
	}

	public function delete($entry)
	{
        try
        {
            $this->_db->beginTransaction();
            if (is_object($entry))
            {
                if (!isset($entry->{$this->_identifier}) || !$entry->{$this->_identifier})
                {
                    return false;
                }

                if ($entry->preDelete() === false) {
                    $this->_db->rollBack();
                    return false;
                }

                $query = $this->registry->db->query();
                $result = $query->delete($this)->where($this->_identifier.' = ?')->limit(1)->execute($entry->{$this->_identifier});

                if (isset($this->_entries[$entry->{$this->_identifier}]))
                {
                    unset($this->_entries[$entry->{$this->_identifier}]);
                }
                $entry->clearDatabaseProperties();

                if ($entry->postDelete() === false) {
                    $this->_db->rollBack();
                    return false;
                }
            }
            else
            {
                $query = $this->registry->db->query();
                $result = $query->delete($this)->where($this->_identifier.' = ?')->limit(1)->execute($entry);

                if (isset($this->_entries[$entry]))
                {
                    unset($this->_entries[$entry]);
                }
            }
            foreach ($this->_relations as $relation => $info) {
                if (isset($info[3]) && $info[3] !== true) {
                    $this->registry->db->query()->delete($info[3])->where($info[1].' = ?')->execute(is_object($entry) ? $entry->{$this->_identifier} : $entry);
                }
            }

            $this->_db->commit();
        } catch (PDOException $e) {
            $this->_db->rollBack();
            return false;
        }
		return $result;
	}

	public function deleteBy($condition, $values, $cleanRefTable = false)
	{
        $query = $this->registry->db->query();
        $result = $query->delete($this)->where($condition)->execute($values);

        if ($cleanRefTable) {
            $this->cleanRefTables();
        }
        
		return $result;
	}

    /**
     * deletes all rows in m:n ref tables which have no related entry in this class
     */
    public function cleanRefTables()
    {
        foreach ($this->_relations as $relation => $info) {
            $stmt = $this->registry->db->query()->select('a_b.id')->from($info[3], 'a_b')->join($this->_table, 'a', 'a_b.'.$info[1].' = a.'.$this->_identifier)->where('a.'.$this->_identifier.' IS NULL')->execute();
            $refTableIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $deleteStmt = $this->registry->db->query()->delete($info[3])->where('id = ?')->prepare();
            foreach ($refTableIds as $refTableId) {
                $deleteStmt->execute(array($refTableId));
            }
        }
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

    public function generateSlug($entry, $source, $field)
    {
        $baseslug = $this->registry->helper->text->sanitize($source, true);
        $id = $entry->getIdentifier() ? $entry->getIdentifier() : 0;
        $sql = 'SELECT count(*) FROM '.$this->_table.' WHERE '.$this->_identifier.' != ? and '.$field.' = ?';
        $stmt = $this->_db->prepare($sql);
        $num = 0;
        $slug = $baseslug;
        do {
            $stmt->execute(array($id, $slug));
            $num--;
            $result = $stmt->fetchColumn();
            $stmt->closeCursor();          
        } while($result && $slug = $baseslug . $num);
        return $slug;
    }
}
