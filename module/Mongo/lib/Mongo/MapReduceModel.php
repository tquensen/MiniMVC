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
        } elseif (isset($this->_properties['value'][$key])) {
            return $this->_properties['value'][$key];
        }
        return isset($this->_properties['_id'][$key]) ? $this->_properties['_id'][$key] : null;
    }

    public function __set($key, $value)
    {
        if (isset($this->_properties[$key])) {
            $this->_properties[$key] = $value;
        } elseif (isset($this->_properties['_id'][$key])) {
            $this->_properties['_id'][$key] = $value;
        }
        $this->_properties['value'][$key] = $value;
    }
    
    public function __isset($key)
	{
        if (!isset($this->_properties[$key]))
        {
            return isset($this->_properties['value'][$key]) || isset($this->_properties['_id'][$key]);
        }
        return true;
	}

    public function __unset($key)
	{
        if (!isset($this->_properties[$key]))
        {
            if (isset($this->_properties['value'][$key])) {
                unset($this->_properties['value'][$key]);
            } else {
                unset($this->_properties['_id'][$key]);
            }
            return;
        }
        unset($this->_properties[$key]);
	}
}