<?php
class {name} extends {name}Base
{
    
    public function preSave()
    {
        if ($this->isNew()) {
            $this->slug = $this->getTable()->generateSlug($this, $this->title, 'slug');
            $this->created_at = time();
        }
        $this->updated_at = time();
    }
    
}