<?php
/**
 * {columns_phpdoc}
 * 
 * @method {name}Repository getRepository()
 */
class {name} extends Mongo_Model
{
    /*
    public function preSave()
    {
        if ($this->isNew()) {
            $this->slug = $this->getRepository()->generateSlug($this, $this->title, 'slug', 32);
            $this->created_at = new MongoDate();
        }
        $this->updated_at = new MongoDate();
    }
     */

    {embedded_methods}

    {relations_methods}

}