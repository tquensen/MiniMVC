<?php
/**
 * @property mixed $_id
 * @property array $value
 */
class Mongo_MapReduceModel extends Mongo_Model
{
    public function __get($key)
    {
        if (isset($this->_properties[$key])) {
            return $this->_properties[$key];
        }
        return isset($this->_properties['value'][$key]) ? $this->_properties['value'][$key] : null;
    }

    public function __set($key, $value)
    {
        if (isset($this->_properties[$key])) {
            $this->_properties[$key] = $value;
        }
        $this->_properties['value'][$key] = $value;
    }
    
    public function __isset($key)
	{
        if (!isset($this->_properties[$key]))
        {
            return isset($this->_properties['value'][$key]);
        }
        return true;
	}

    public function __unset($key)
	{
        if (!isset($this->_properties[$key]))
        {
            unset($this->_properties['value'][$key]);
            return;
        }
        unset($this->_properties[$key]);
	}
}