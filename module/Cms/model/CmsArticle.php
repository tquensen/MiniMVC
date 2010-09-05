<?php
/**
 * @property CmsArticleTable $_table
 * @method CmsArticleTable getTable()
 */
class CmsArticle extends MiniMVC_Model
{
    public function  preInsert()
    {
        $this->slug = $this->getTable()->generateSlug($this, $this->title, 'slug');
    }
    
}