<?php
class Test_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        $this->view->form = Doctrine_Core::getTable('TestExampleModel')->getRecord()->getForm();
        if ($this->view->form->validate()) {
            $this->view->form->updateRecord()->save();
            return 'YEAH!';
        }
        return $this->view->parse('default/index');
    }

    public function showAction($params)
    {
        $this->view->entry = Doctrine_Core::getTable('TestExampleModel')->getTranslatedRecord($params['id'], $this->registry->settings->currentLanguage);
        return $this->view->parse('default/show');
    }
}
