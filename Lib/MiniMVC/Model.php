<?php
class MiniMVC_Model
{
	protected $_properties = array();
	protected $_table = null;
	protected $_isNew = true;

	public function __construct($table = null)
	{
        if ($table) {
            $this->_table = $table;
        } else {
            $tableName = get_class($this).'Table';
            $this->_table = new $tableName();
        }
	}

    public function isNew($newStatus = null)
    {
        if ($newStatus !== null) {
            $this->_isNew = (bool) $newStatus;
        }
        return $this->_isNew;
    }

    public function getForm($options = array())
	{
		$formClass = get_class($this).'Form';
		return new $formClass($this, $options);
	}

    public function getTable()
	{
		return $this->_table;
	}

	public function __get($key)
	{
        return isset($this->_properties[$key]) ? $this->_properties[$key] : null;
	}

	public function __set($key, $value)
	{
		$this->_properties[$key] = $value;
	}

    public function __isset($key)
	{
        return isset($this->_properties[$key]);
	}

    public function __unset($key)
	{
        if (isset($this->_properties[$key])) {
            unset($this->_properties[$key]);
        }
	}

	public function save()
	{
		return ($this->_table && method_exists($this->_table, 'save')) ? $this->_table->save($this) : false;
	}

	public function delete()
	{
		return ($this->_table && method_exists($this->_table, 'delete')) ? $this->_table->delete($this) : false;
	}
}