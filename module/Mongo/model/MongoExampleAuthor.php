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

    public function preSave()
    {
        if ($this->isNew()) {
            $this->created_at = new MongoDate();
        }
        $this->updated_at = new MongoDate();
    }

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
        $query = (array)$query;
        $query['author_id'] = $this->_id;
        return MongoExampleArticleRepository::get()->find($query = array(), $sort = array(), $limit = null, $skip = null);
    }

    /**
     * @param MongoExampleArticle|mixed $related either a MongoExampleArticle object, a MongoExampleArticle->_id-value or an array with multiple MongoExampleArticles
     * @return bool
     */
    public function setArticles($related)
    {
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->setArticles($rel);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof MongoExampleArticle)) {
            $related = MongoExampleArticleRepository::get()->findOne($related);
        }
        if (!$related) {
            throw new InvalidArgumentException('Could not find valid MongoExampleArticle');
        }
        $related->author_id = $this->_id;
        return $related->save();
    }

    /**
     * @param MongoExampleArticle|mixed $related either a MongoExampleArticle object, a MongoExampleArticle->_id-value  or an array with multiple MongoExampleArticles
     * @return bool
     */
    public function removeArticles($related)
    {
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->removeArticles($rel);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof MongoExampleArticle)) {
            $related = MongoExampleArticleRepository::get()->findOne($related);
        }
        if (!$related) {
            throw new InvalidArgumentException('Could not find valid MongoExampleArticle');
        }
        if ($related->author_id != $this->_id) {
            return false;
        }
        $related->author_id = null;
        return $related->save();
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
        $query = (array)$query;
        $query['author_id'] = $this->_id;
        return MongoExampleCommentRepository::get()->find($query = array(), $sort = array(), $limit = null, $skip = null);
    }

    /**
     * @param MongoExampleComment|mixed $related either a MongoExampleComment object, a MongoExampleComment->_id-value or an array with multiple MongoExampleComments
     * @return bool
     */
    public function setComments($related)
    {
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->setComments($rel);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof MongoExampleComment)) {
            $related = MongoExampleCommentRepository::get()->findOne($related);
        }
        if (!$related) {
            throw new InvalidArgumentException('Could not find valid MongoExampleComment');
        }
        $related->author_id = $this->_id;
        return $related->save();
    }

    /**
     * @param MongoExampleComment|mixed $related either a MongoExampleComment object, a MongoExampleComment->_id-value  or an array with multiple MongoExampleComments
     * @return bool
     */
    public function removeComments($related)
    {
        if (is_array($related)) {
            foreach ($related as $rel) {
                $this->removeComments($rel);
            }
            return true;
        }
        if (!is_object($related) || !($related instanceof MongoExampleComment)) {
            $related = MongoExampleCommentRepository::get()->findOne($related);
        }
        if (!$related) {
            throw new InvalidArgumentException('Could not find valid MongoExampleComment');
        }
        if ($related->author_id != $this->_id) {
            return false;
        }
        $related->author_id = null;
        return $related->save();
    }

}