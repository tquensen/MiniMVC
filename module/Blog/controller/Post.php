<?php
class Blog_Post_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        //list view

        $showPerPage = 2;
        $currentPage = !empty($_GET['p']) ? $_GET['p'] : 1;
        $query = BlogPostTable::getInstance()->loadWithRelations(array('a.Tags', 't', true),'status = ?', 'published', 'a.created_at DESC, a.id DESC', $showPerPage, ($currentPage - 1) * $showPerPage, true, 'query');

        $this->view->entries = $query->build();

        $this->view->pager = $this->registry->helper->pager->get(
                $query->count(),
                $showPerPage,
                $this->registry->helper->url->get('blog.index') . '(?p={page})',
                $currentPage,
                7,
                false
        );

        return $this->view->parse();
    }

    public function showAction($params)
    {
        $model = BlogPostTable::getInstance()->loadOneWithRelationsBy(array(array('a.Comments', 'c', true), array('a.Tags', 't', true)), 'a.slug = ?', $params['slug'], 'c.created_at ASC, c.id ASC', 0, false);
        if (!$model) {
            return $this->delegate404();
        }
        if ($model->status == 'draft' && !$this->registry->guard->userHasRight('publish')) {
            return $this->delegate403();
        }
        $this->view->model = $model;
        return $this->view->parse();
    }

    public function createAction($params)
    {
        $this->view->form = BlogPostTable::getInstance()->getForm();
        if ($this->view->form->validate())
        {
            $model = $this->view->form->updateModel();
            if (!$model->save()) {
                $this->view->form->title->setError('Es gab einen Fehler beim speichern! Probier es doch nochmal, noob!');
                $this->view->form->errorRedirect();
            }
            $this->refreshBlogPostTags($model, $this->view->form->tags->value);
            return $this->redirect('blog.show', array('slug' => $model->slug));
        }
        
        return $this->view->parse();
    }

    public function editAction($params)
    {

        if (!$params['model']) {
            return $this->delegate404();
        }
        $this->view->form = BlogPostTable::getInstance()->getForm($params['model']);
        if ($this->view->form->validate())
        {
            $model = $this->view->form->updateModel();
            if (!$model->save()) {
                $this->view->form->title->setError('Es gab einen Fehler beim speichern! Probier es doch nochmal, noob!');
                $this->view->form->errorRedirect();
            }
            $this->refreshBlogPostTags($model, $this->view->form->tags->value, true);

            return $this->redirect('blog.show', array('slug' => $model->slug));
        }
        
        return $this->view->parse();
    }

    protected function refreshBlogPostTags($model, $tagList, $delete = false)
    {
        if ($delete) {
            $model->unlinkTags(true);
        }
        foreach ($tagList as $tagId) {
            $model->linkTags($tagId);
        }
    }
}
