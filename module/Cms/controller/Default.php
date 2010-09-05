<?php
class Cms_Default_Controller extends MiniMVC_Controller
{
    public function createAction($params)
    {
        $this->view->form = CmsArticleTable::getInstance()->getForm();
        if ($this->view->form->validate())
        {
            $model = $this->view->form->updateModel();
            return $this->redirect('cms.show', array('slug' => $model->slug));
        }
        return $this->view->parse();
    }

    public function showAction($params)
    {
        $this->view->article = CmsArticleTable::getInstance()->loadOneBy('slug = ?', $params['slug']);
        if (!$this->view->article) {
            $this->delegate404();
        }

        if ($this->view->article->status == 'draft' && !$this->registry->guard->userHasRight($this->registry->settings->get('config/cms/authorRights'))) {
            return $this->delegate403();
        }
        return $this->view->parse();
    }

    public function editAction($params)
    {
        $article = CmsArticleTable::getInstance()->loadOneBy('slug = ?', $params['slug']);
        if (!$article) {
            $this->delegate404();
        }
        $this->view->form = CmsArticleTable::getInstance()->getForm($article);
        if ($this->view->form->validate())
        {
            $model = $this->view->form->updateModel();
            $model->save();
            return $this->redirect('cms.show', array('slug' => $model->slug));
        }
        return $this->view->parse();
    }
}
