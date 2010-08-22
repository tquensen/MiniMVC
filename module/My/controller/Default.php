<?php

class My_Default_Controller extends MiniMVC_Controller
{

    public function indexAction($params)
    {
        $this->view->params = $params;
        //return $this->view->parse('default/index.php', 'My');

        $this->view->pager = $this->registry->helper->Pager->get(230, 20, $this->registry->helper->Url->get('test') . '(?p={page})', (isset($_GET['p']) ? $_GET['p'] : 1), 7);


        //var_dump(new DevFischArtForm());//$fisch->getForm());
        /*
          $art = new DevFischArt();
          $art->name='Käsefisch';
          $fisch = new DevFisch();
          $fisch->DevFischArt=$art;
          $fisch->name = 'Käsefisch Nr. 1';
          $this->view->fisch = $fisch;
         */

        return $this->view->parse();
    }

    public function testAction($params) {
        $this->view->params = $params;
        return $this->view->parse();
    }

    public function formAction($params)
    {
        $form = new MiniMVC_Form(array('name' => 'formTest', 'enctype' => 'multipart/form-data'));
        $form->setElement(new MiniMVC_Form_Element_File('filetest'));
        $form->setElement(new MiniMVC_Form_Element_Submit('submit', array('label' => 'Jo!')));
        if ($form->validate()) {
            return '(' . $form->filetest->fileError . ') ' . $form->filetest->fileName.': '.$form->filetest->fileType;
        }
        $this->view->form = $form;
        return $this->view->parse();
    }

}