<?php

class Mongo_Embedded
{
    protected $_properties = array();

    public function __construct(&$data = array())
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
}

