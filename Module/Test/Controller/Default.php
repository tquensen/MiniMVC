<?php
class Test_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        $this->view->form = TestExampleModelTable::getInstance()->getRecord()->getForm();
        if ($this->view->form->validate()) {
            $this->view->form->updateRecord()->save();
            return 'YEAH!';
        }
        return $this->view->parse();
    }

    public function showAction($params)
    {
        if ($this->view->entry = TestExampleModelTable::getInstance()->getTranslatedRecord($params['id'], $this->registry->settings->currentLanguage)) {
            return $this->view->parse();
        } else {
            return $this->delegate404();
        }
        
    }
}
