<?php
class MiniMVC_Collection implements ArrayAccess, Countable, Iterator
{
    protected $entries = array();

    
    public function count()
    {
        return count($this->entries);
    }

    public function add($entries) {
        if (is_object($entries) && $entries instanceof MiniMVC_Model) {
            $this->__set($entries->getIdentifier(), $entries);
        } elseif (is_object($entries) && $entries instanceof MiniMVC_Collection) {
            foreach ($entries as $entry) {
                $this->__set($entry->getIdentifier(), $entry);
            }
        }
        throw new InvalidArgumentException('The value must be a MiniMVC_Model or MiniMVC_Collection instance!');
    }

    public function __get($key)
	{
        return isset($this->entries[$key]) ? $this->entries[$key] : null;
	}

	public function __set($key, $value)
	{
        if (is_object($value) && $value instanceof MiniMVC_Model) {
            $value->setCollection($this);
            $this->entries[$key] = $value;
            return;
        }
        throw new InvalidArgumentException('The value must be a MiniMVC_Model instance!');
	}

    public function __isset($key)
	{
        return isset($this->entries[$key]);
	}

    public function __unset($key)
	{
        if (isset($this->entries[$key])) {
            unset($this->entries[$key]);
        }
	}

    public function offsetSet($offset, $data)
    {
        if (is_object($data) && $data instanceof MiniMVC_Model) {
            $data->setCollection($this);
            if ($offset === null) {
                if ($id = $data->getIdentifier()) {
                    $this->entries[$id] = $data;
                } else {
                    $this->entries[min(array_keys($this->entries)) - 1] = $data;
                }
            } else {
                $this->entries[$offset] = $data;
            }
            return;
        }
        throw new InvalidArgumentException('The value must be a MiniMVC_Model instance!');
    }

    public function offsetGet($offset)
    {
        return isset($this->entries[$offset]) ? $this->entries[$offset] : null;
    }

    public function offsetExists($offset)
    {
        return isset($this->entries[$offset]);
    }

    public function offsetUnset($offset)
    {
        if (isset($this->entries[$offset])) {
            unset($this->entries[$offset]);
        }
    }

    public function getFirst()
    {
        return reset($this->entries);
    }

    public function current()
    {
        return current($this->entries);
    }

    public function key()
    {
        return key($this->entries);
    }

    public function next()
    {
        return next($this->entries);
    }

    public function rewind()
    {
        return reset($this->entries);
    }

    public function valid()
    {
        return key($this->entries) !== null;
    }

    /**
     *
     * examples:
     * $fields could look like the following:
     * array(
     *     'id', 'title', 'description'
     * )
     * or
     * array(
     *     'id', 'title', 'description', 'relations' => array(
     *          'Comments' => true, //export all fields of the related comment model
     *          'User' => array('id', 'username', 'email'), //export the id, username and email of the related users
     *          'Tags' => array(
     *              'id', 'title', 'relations' => array( //you can also fetch relations of relations
     *                  'Posts' => array('id') //get the post-ids related to each tag
     *              )
     *          )
     *      )
     * )
     *
     * @param array|bool $fields true to export all model properties or an array of property-names to export. add a key 'relations' with a value structured like this (true or array) to include related models
     * @return array
     */
    public function toArray($fields = true)
    {
        $return = array();
        foreach ($this->entries as $entry) {
            $return[] = $entry->toArray($fields);
        }
        return $return;
    }
}
?>
