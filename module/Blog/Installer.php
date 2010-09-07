<?php

class Blog_Installer extends MiniMVC_Installer
{

    public function install($installedVersion)
    {
        try
        {
            $blog = new BlogPostTable();
            $blog->install($installedVersion);
            $tag = new BlogTagTable();
            $tag->install($installedVersion);
            $comment = new BlogCommentTable();
            $comment->install($installedVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

    public function uninstall($installedVersion)
    {
        try
        {
            $blog = new BlogPostTable();
            $blog->uninstall($installedVersion);
            $tag = new BlogTagTable();
            $tag->uninstall($installedVersion);
            $comment = new BlogCommentTable();
            $comment->uninstall($installedVersion);
        }
        catch(Exception $e)
        {
            $this->message = $e->getMessage();
            return false;
        }
        return true;
    }

}
