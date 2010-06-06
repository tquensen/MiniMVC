<?php

class Dev_Install_Controller extends MiniMVC_Controller
{

    public function moduleAction($params)
    {
        $form = $this->getInstallForm();

        if ($form->validate()) {
            $this->view->module = $form->module->value;

            if (file_exists(BASEPATH . '/Module/' . $this->view->module . '/Installer.php')) {
                $class = 'Module_' . $this->view->module . '_Installer';
                $installer = new $class();
                if ($form->type->value == 'install') {
                    $this->view->status = (bool)$installer->install();
                } elseif ($form->type->value == 'uninstall') {
                    $this->view->status = (bool)$installer->uninstall();
                }
            } else {
                $this->view->status = false;
            }
            return $this->view->parse('install');
        } else {
            $this->view->form = $form;
            return $this->view->parse('installForm');
        }
    }

    protected function getInstallForm()
    {
        $modules = MiniMVC_Registry::getInstance()->settings->modules;
        $modules = array_combine($modules, $modules);
        $modules = array('0' => $this->view->t->installFormModuleChoose) + $modules;
        $form = new MiniMVC_Form(null, array('name' => 'DevInstaller'));
        $form->addElement(
                new MiniMVC_Form_Element_Select('module',
                        array('label' => $this->view->t->installFormModule, 'options' => $modules, 'errorMessage' => $this->view->t->installFormModuleInvalid),
                        new MiniMVC_Form_Validator_Required(array('errorMessage' => $this->view->t->installFormModuleRequired))
                )
        );
        $form->addElement(new MiniMVC_Form_Element_Select('type', array('label' => $this->view->t->installFormType, 'options' => array('install' => 'install', 'uninstall' => 'uninstall'))));
        $form->addElement(new MiniMVC_Form_Element_Submit('submit', array('label' => $this->view->t->installFormSubmit)));

        $form->setValues();

        return $form;
    }

}

