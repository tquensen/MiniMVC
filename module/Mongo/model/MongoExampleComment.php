<?php

/**
 * @property MongoId $_id
 * @property MongoId $article_id
 * @property string $author_id
 * @property string $content
 * @property MongoDate $created_at
 * @property MongoDate $updated_at
 * 
 * 
 * @method MongoExampleCommentRepository getRepository()
 */
class MongoExampleComment extends Mongo_Model
{

    public function preSave()
    {
        if ($this->isNew()) {
            $this->created_at = new MongoDate();
        }
        $this->updated_at = new MongoDate();
    }

    /**
     * @return MongoExampleArticle
     */
    public function getArticle()
    {
        return MongoExampleArticleRepository::get()->findOne(array('_id' => $this->article_id));
    }

    /**
     * @param MongoExampleArticle|mixed $related either a MongoExampleArticle object or a MongoExampleArticle->_id-value
     * @return bool
     */
    public function setArticle($related)
    {
        if (is_object($related) && $related instanceof MongoExampleArticle) {
            $this->article_id = $related->_id;
        } else {
            $this->article_id = $related;
        }
        return $this->save();
    }

    /**
     * @return bool
     */
    public function removeArticle()
    {
        $this->article_id = null;
        return $this->save();
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
     * @return bool
     */
    public function setAuthor($related)
    {
        if (is_object($related) && $related instanceof MongoExampleAuthor) {
            $this->author_id = $related->_id;
        } else {
            $this->author_id = $related;
        }
        return $this->save();
    }

    /**
     * @return bool
     */
    public function removeAuthor()
    {
        $this->author_id = null;
        return $this->save();
    }

}