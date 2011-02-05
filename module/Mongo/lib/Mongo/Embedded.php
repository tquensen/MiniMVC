<?php

class Mongo_Embedded
{
    protected $_properties = array();
    protected $_sortBy = null;
    protected $_sortDesc = false;

    public function __construct($data = array())
    {
        $this->_properties = &$data;
    }

    public function __get($key)
    {
        return isset($this->_properties[$key]) ? $this->_properties[$key] : null;
    }

    public function __set($key, $value)
    {
        $this->_properties[$key] = $value;
    }

    public function &getData()
    {
        return $this->_properties;
    }

    public function sort($entries, $property, $sortDesc = false)
    {
        $this->_sortBy = $property;
        $this->_sortDesc = (bool) $sortDesc;
        uasort($entries, array($this, '_sort'));
        return $entries;
    }

    public function _sort($a, $b)
    {
        if ($a->{$this->_sortBy} == $b{$this->_sortBy}) {
            return 0;
        }
        if ($this->_sortDesc) {
            return ($a->{$this->_sortBy} < $b->{$this->_sortBy}) ? 1 : -1;
        } else {
            return ($a->{$this->_sortBy} < $b->{$this->_sortBy}) ? -1 : 1;
        }
    }
}

