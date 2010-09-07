<?php
/**
 * @property BlogPostTable $_table
 * @method BlogPostTable getTable()
 */
class BlogPost extends MiniMVC_Model
{
    public function  preInsert()
    {
        $this->slug = $this->getTable()->generateSlug($this, $this->title, 'slug');
    }
}