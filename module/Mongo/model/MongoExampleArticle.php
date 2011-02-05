<?php
/**
 * @property MongoId $_id
 * @property string $slug
 * @property string $title
 * @property string $content
 * @property array $comments
 * @property MongoId $author_id
 * @property MongoDate $created_at
 * @property MongoDate $updated_at
 * 
 * 
 * @method MongoExampleArticleRepository getRepository()
 */
class MongoExampleArticle extends Mongo_Model
{
    
    public function preSave()
    {
        if ($this->isNew()) {
            $this->slug = $this->getRepository()->generateSlug($this, $this->title, 'slug', 32);
            $this->created_at = new MongoDate();
        }
        $this->updated_at = new MongoDate();
    }
     

    
    /**
     *
     * @param int|bool $key the identifier of a embedded or true to return all
     * @param string $sortBy (optional) if $key == true, order the entries by this property, null to keep the db order
     * @param bool $sortDesc false (default) to sort ascending, true to sort descending
     * @return MongoEmbeddedComment|array
     */
    public function getEComments($key = true, $sortBy = null, $sortDesc = false)
    {
        return $this->getEmbedded('EComments', $key, $sortBy, $sortDesc);
    }

    /**
     *
     * @param MongoEmbeddedComment|array $data a MongoEmbeddedComment object or an array representing a MongoEmbeddedComment or an array with multiple MongoEmbeddedComment
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function setEComments($data, $save = true)
    {
        return $this->setEmbedded('EComments', $data, $save);
    }

    /**
     * removes the chosen MongoEmbeddedComments (or all for $key = true) from the embedded list
     *
     * @param mixed $key one or more keys for MongoEmbeddedComment objects or true to remove all
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function removeEComments($key = true, $save = true)
    {
        return $this->removeEmbedded('EComments', $key, $save);
    }
    

    
    /**
     *
     * @param array $query Additional fields to filter.
     * @param array $sort The fields by which to sort.
     * @param int $limit The number of results to return.
     * @param int $skip The number of results to skip.
     * @return array
     */
    public function getComments($query = array(), $sort = array(), $limit = null, $skip = null)
    {
        return $this->getRelated('Comments', $query, $sort, $limit, $skip);
    }

    /**
     * @param MongoExampleComment|mixed $related either a MongoExampleComment object, a MongoExampleComment->_id-value or an array with multiple MongoExampleComments
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function setComments($related, $save = true)
    {
        return $this->setRelated('Comments', $related, $save = true);
    }

    /**
     * @param MongoExampleComment|mixed $related true to remove all objects or either a MongoExampleComment object, a MongoExampleComment->_id-value  or an array with multiple MongoExampleComments
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function removeComments($related = true, $save = true)
    {
        return $this->removeRelated('Comments', $related, $save);
    }
    

    /**
     *
     * @return MongoExampleAuthor|null
     */
    public function getAuthor()
    {
        return $this->getRelated('Author');
    }

    /**
     * @param MongoExampleAuthor|mixed $related either a MongoExampleAuthor object or a MongoExampleAuthor->_id-value
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function setAuthor($related, $save = true)
    {
        return $this->setRelated('Author', $related, $save = true);
    }

    /**
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function removeAuthor($save = true)
    {
        return $this->removeRelated('Author', true, $save);
    }
    

}