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
     * @param bool|int $key the key of the model to get or true (default) to get all
     * @return array
     */
    public function getEComments($key = true)
    {
        if ($key === true) {
            $return = array();
            foreach ($this->_properties['comments'] as $currentKey => $entry) {
                $return[$currentKey] = new MongoEmbeddedComment($entry);
            }
            return $return;
        }
        return isset($this->_properties['comments'][$key]) ? new MongoEmbeddedComment($this->_properties['comments'][$key]) : null;
    }

    /**
     * overwrites EComments with the data provided
     *
     * @param MongoEmbeddedComment|array $related if $key = true, an array of MongoEmbeddedComment objects or an array of arrays representing MongoEmbeddedComments, if $key = int: a MongoEmbeddedComment or an array representing a MongoEmbeddedComment
     * @param bool|int $key the key of the model to overwrites or true (default) to overwrite all
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function setEComments($related, $key = true, $save = true)
    {
        if ($key === true) {
            $entries = array();
            foreach ($related as $rel) {
                if (is_object($related) && $related instanceof Mongo_Embedded) {
                    $related = $related->getData();
                }
                $entries[] = $related;
            }

            $this->_properties['comments'] = $entries;
        } else {
            if (!isset($this->_properties['comments'][$key])) {
                return false;
            }
            if (is_object($related) && $related instanceof Mongo_Embedded) {
                $related = $related->getData();
            }
            $this->_properties['comments'][$key] = $related;
        }
        
        return $save ? $this->save() : true;
    }

    /**
     * adds the provided MongoEmbeddedComments to the EComments
     *
     * @param array $related an array of MongoEmbeddedComment objects or an array of arrays representing MongoEmbeddedComments or an array with multiple MongoEmbeddedComments (use multiple)
     * @param bool $multiple set to true if you are passing multiple MongoEmbeddedComments
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function addEComments($related, $multiple = false, $save = true)
    {
        if (!$multiple) {
            $related = array($related);
        }
        $entries = array();
        foreach ($related as $rel) {
            if (is_object($related) && $related instanceof Mongo_Embedded) {
                $related = $related->getData();
            }
            $entries[] = $related;
        }
        $currentEntries = (array) $this->_properties['comments'];
        $this->_properties['comments'] = array_merge($currentEntries, $entries);

        return $save ? $this->save() : true;
    }

    /**
     * removed the chosen MongoEmbeddedComment (or all for $key = true) from the EComments and reindexes the comments array
     *
     * @param bool|int $key the key of the model to remove or true (default) to remove all
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function removeEComments($key = true, $save = true)
    {
        if ($key === true) {
             $this->comments = array();
        } else {
            unset($this->_properties['comments'][$key]);
            $this->_properties['comments'] = array_values($this->_properties['comments']);
        }
        
        return $save ? $this->save() : true;
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
        $query = (array) $query;
        $query['article_id'] = $this->_id;
        return MongoExampleCommentRepository::get()->find($query = array(), $sort = array(), $limit = null, $skip = null);
    }

    /**
     * @param MongoExampleComment|mixed $related either a MongoExampleComment object, a MongoExampleComment->_id-value or an array with multiple MongoExampleComments
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function setComments($related, $save = true)
    {
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->setComments($rel, $save);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof MongoExampleComment)) {
            $related = MongoExampleCommentRepository::get()->findOne($related);
        }
        if (!$related) {
            throw new InvalidArgumentException('Could not find valid MongoExampleComment');
        }
        $related->article_id = $this->_id;
        return $save ? $related->save() : true;
    }

    /**
     * @param MongoExampleComment|mixed $related either a MongoExampleComment object, a MongoExampleComment->_id-value  or an array with multiple MongoExampleComments
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function removeComments($related, $save = true)
    {
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->removeComments($rel, $save);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof MongoExampleComment)) {
            $related = MongoExampleCommentRepository::get()->findOne($related);
        }
        if (!$related) {
            throw new InvalidArgumentException('Could not find valid MongoExampleComment');
        }
        if ($related->article_id != $this->_id) {
            return false;
        }
        $related->article_id = null;
        return $save ? $related->save() : true;
    }
    

    /**
     * @return MongoExampleAuthor
     */
    public function getAuthor()
    {
        return MongoExampleAuthorRepository::get()->findOne(array('_id' => $this->author_id));
    }

    /**
     * @param MongoExampleAuthor|mixed $related either a MongoExampleAuthor object or a MongoExampleAuthor->_id-value
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function setAuthor($related, $save = true)
    {
        if (is_object($related) && $related instanceof MongoExampleAuthor) {
            $this->author_id = $related->_id;
        } else {
            $this->author_id = $related;
        }
        return $save ? $this->save() : true;
    }

    /**
     * @param bool $save set to false to prevent a save() call
     * @return bool
     */
    public function removeAuthor($save = true)
    {
        $this->author_id = null;
        return $save ? $this->save() : true;
    }
    

}