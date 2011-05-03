<?php
/**
 * @property string $_id
 * @property int $seq
 * 
 * 
 * @method MongoSequenceRepository getRepository()
 */
class MongoSequence extends Mongo_Model
{
   
    public function preSave()
    {
        if ($this->isNew()) {
            $this->seq = 0;
        }
    }
    
    public function getCurrentId()
    {
        return $this->getRepository()->getCurrentId($this->_id);
    }
    
    public function generateNextId()
    {
        return $this->getRepository()->generateNextId($this->_id);
    }
    
    public function setId($newId)
    {
        return $this->getRepository()->setId($this->_id, $newId);
    }
}