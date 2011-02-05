<?php
/**
 * @property MongoId $_id
 * @property string $name
 * @property MongoDate $created_at
 * @property MongoDate $updated_at
 * 
 * 
 * @method MongoExampleAuthorRepository getRepository()
 */
class MongoExampleAuthor extends Mongo_Model
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

    

    
    /**
     *
     * @param array $query Additional fields to filter.
     * @param array $sort The fields by which to sort.
     * @param int $limit The number of results to return.
     * @param int $skip The number of results to skip.
     * @return array
     */
    public function getArticles($query = array(), $sort = array(), $limit = null, $skip = null)
    {
        return $this->getRelated('Articles', $query, $sort, $limit, $skip);
    }

    /**
     * @param MongoExampleArticle|mixed $related either a MongoExampleArticle object, a MongoExampleArticle->_id-value or an array with multiple MongoExampleArticles
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function setArticles($related, $save = true)
    {
        return $this->setRelated('Articles', $related, $save = true);
    }

    /**
     * @param MongoExampleArticle|mixed $related true to remove all objects or either a MongoExampleArticle object, a MongoExampleArticle->_id-value  or an array with multiple MongoExampleArticles
     * @param mixed $save set to null to prevent a save() call, otherwise call save($save)
     * @return bool
     */
    public function removeArticles($related = true, $save = true)
    {
        return $this->removeRelated('Articles', $related, $save);
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
    

}